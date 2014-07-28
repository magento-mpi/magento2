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
    /** @var ObjectManager */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
    }

    public function testConvertFromModel()
    {
        /** @var \Magento\ConfigurableProduct\Service\V1\Data\OptionConverter $converter */
        $converter = $this->objectManager->getObject('Magento\ConfigurableProduct\Service\V1\Data\OptionConverter');
        $converterMock = $this->getMockBuilder('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getData', 'getLabel', '__sleep', '__wakeup'])
            ->getMock();

        $prices = ['value_index' => 1, 'pricing_value' => 12, 'is_percent' => true];
        $converterMock->expects($this->at(0))->method('getData')->with('prices')->will($this->returnValue([$prices]));
        $converterMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $converterMock->expects($this->at(2))->method('getData')->with('attribute_id')->will($this->returnValue(2));
        $converterMock->expects($this->once())->method('getLabel')->will($this->returnValue('Test Label'));
        $converterMock->expects($this->at(4))->method('getData')->with('position')->will($this->returnValue(3));
        $converterMock->expects($this->at(5))->method('getData')->with('use_default')->will($this->returnValue(true));
        /** @var \Magento\ConfigurableProduct\Service\V1\Data\Option $option */
        $option = $converter->convertFromModel($converterMock);

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
}
