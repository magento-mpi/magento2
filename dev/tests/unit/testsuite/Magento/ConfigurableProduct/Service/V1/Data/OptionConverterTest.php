<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data;

use Magento\TestFramework\Helper\ObjectManager;

class OptionConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\OptionConverter
     */
    private $converter;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configurableAttribute;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    private $option;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeFactory;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\Option\ValueConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $valueConverter;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\Option\Value|\PHPUnit_Framework_MockObject_MockObject
     */
    private $value;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->configurableAttribute = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute'
        )
            ->setMethods(
                [
                    'setData',
                    'getData',
                    'addData',
                    '__wakeup',
                    'getId',
                    'setId',
                    'getAttributeId',
                    'setAttributeId',
                    'setValues',
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->option = $this->getMockBuilder('Magento\ConfigurableProduct\Service\V1\Data\Option')
            ->setMethods(['__toArray', 'getValues'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->valueConverter = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Service\V1\Data\Option\ValueConverter'
        )
            ->setMethods(['convertArrayFromData'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->value = $this->getMockBuilder('Magento\ConfigurableProduct\Service\V1\Data\Option\Value')
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeFactory = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeFactory->expects($this->any())->method('create')
            ->will($this->returnValue($this->configurableAttribute));

        $this->converter = $helper->getObject(
            'Magento\ConfigurableProduct\Service\V1\Data\OptionConverter',
            ['attributeFactory' => $this->attributeFactory, 'valueConverter' => $this->valueConverter]
        );
    }

    public function testConvertFromModel()
    {
        $converterMock = $this->getMockBuilder('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getData', 'getLabel', '__sleep', '__wakeup', 'getProductAttribute'])
            ->getMock();

        $productAttribute = $this->getMockBuilder('Magento\Catalog\Model\Resource\Eav\Attribute')
            ->disableOriginalConstructor()
            ->setMethods(['getFrontend', '__wakeup'])
            ->getMock();

        $frontend = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\Frontend\DefaultFrontend')
            ->disableOriginalConstructor()
            ->setMethods(['getInputType'])
            ->getMock();

        $productAttribute->expects($this->any())->method('getFrontend')->will($this->returnValue($frontend));
        $frontend->expects($this->once())->method('getInputType')->will($this->returnValue('select'));

        $prices = ['value_index' => 1, 'pricing_value' => 12, 'is_percent' => true];
        $converterMock->expects($this->at(0))->method('getData')->with('prices')->will($this->returnValue([$prices]));
        $converterMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $converterMock->expects($this->at(2))->method('getData')->with('attribute_id')->will($this->returnValue(2));
        $converterMock->expects($this->once())->method('getLabel')->will($this->returnValue('Test Label'));
        $converterMock->expects($this->any())->method('getProductAttribute')->will($this->returnValue($productAttribute));
        $converterMock->expects($this->at(5))->method('getData')->with('position')->will($this->returnValue(3));
        $converterMock->expects($this->at(6))->method('getData')->with('use_default')->will($this->returnValue(true));

        /** @var \Magento\ConfigurableProduct\Service\V1\Data\Option $option */
        $option = $this->converter->convertFromModel($converterMock);

        $this->assertEquals(1, $option->getId());
        $this->assertEquals(2, $option->getAttributeId());
        $this->assertEquals('Test Label', $option->getLabel());
        $this->assertEquals(3, $option->getPosition());

        /** @var \Magento\ConfigurableProduct\Service\V1\Data\Option\Value $value */
        $value = \current($option->getValues());
        $this->assertEquals(1, $value->getIndex());
        $this->assertEquals(12, $value->getPrice());
        $this->assertEquals(true, $value->isPercent());
    }

    public function testGetModelFromData()
    {
        $data = ['data'];
        $id = 33;
        $this->configurableAttribute->expects($this->any())->method('getData')->will($this->returnValue($data));
        $this->configurableAttribute->expects($this->once())->method('setData')->with($this->equalTo($data));
        $this->configurableAttribute->expects($this->once())->method('addData')->with($this->equalTo($data));
        $this->configurableAttribute->expects($this->any())->method('getId')->will($this->returnValue($id));
        $this->configurableAttribute->expects($this->once())->method('setId')->with($this->equalTo($id));
        $this->configurableAttribute->expects($this->any())->method('getAttributeId')->will($this->returnValue($id));
        $this->configurableAttribute->expects($this->once())->method('setAttributeId')->with($this->equalTo($id));
        $this->configurableAttribute->expects($this->any())->method('getValues')->will($this->returnValue($data));
        $this->configurableAttribute->expects($this->any())->method('setValues')->with($this->equalTo($data));

        $this->option->expects($this->any())->method('getValues')->will($this->returnValue([$this->value]));
        $this->option->expects($this->any())->method('__toArray')->will($this->returnValue($data));

        $this->valueConverter->expects($this->any())->method('convertArrayFromData')
            ->with($this->equalTo($this->value))
            ->will($this->returnValue($data[0]));

        $result = $this->converter->getModelFromData($this->option, $this->configurableAttribute);
        $this->assertEquals($this->configurableAttribute, $result);
    }
}
