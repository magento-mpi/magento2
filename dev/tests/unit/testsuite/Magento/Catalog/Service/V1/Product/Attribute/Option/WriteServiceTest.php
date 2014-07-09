<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\Catalog\Service\V1\Data\Eav\Option\Label;
use Magento\Catalog\Service\V1\Product\MetadataServiceInterface as ProductMetadataServiceInterface;
use Magento\TestFramework\Helper\ObjectManager;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attribute;
    
    /**
     * @var \Magento\Catalog\Service\V1\Product\Attribute\Option\WriteServiceInterface
     */
    protected $service;

    public function setUp()
    {
        $this->eavConfig = $this->getMockBuilder('Magento\Eav\Model\Config')
            ->disableOriginalConstructor()->getMock();
        $this->attribute = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
            ->disableOriginalConstructor()->getMock();

        $this->service = (new ObjectManager($this))->getObject(
            'Magento\Catalog\Service\V1\Product\Attribute\Option\WriteService',
            array(
                'eavConfig' => $this->eavConfig
            )
        );
    }
    
    /**
     * Test for retrieving attribute options
     */
    public function testAddOption()
    {
        $attributeCode = 'attr_code';

        $label = $this->buildLabel('st 42', 42);

        $this->attribute->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->attribute->expects($this->any())
            ->method('usesSource')
            ->will($this->returnValue(true));

        $this->attribute->expects($this->at(2))
            ->method('__call')
            ->with('setDefault', [['new_option']]);

        $this->attribute->expects($this->at(3))
            ->method('__call')
            ->with(
                'setOption',
                [
                    [
                        'value' => ['new_option' => ['label', 42 => 'st 42']],
                        'order' => ['new_option' => 10],
                    ]
                ]
            );

        $this->attribute->expects($this->any())
            ->method('save');

        $option = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Eav\Option')
            ->disableOriginalConstructor()->getMock();

        $option->expects($this->any())
            ->method('getLabel')->will($this->returnValue('label'));

        $option->expects($this->any())
            ->method('getStoreLabels')
            ->will(
                $this->returnValue(
                    [
                        $label
                    ]
                )
            );

        $option->expects($this->any())
            ->method('getOrder')->will($this->returnValue(10));

        $option->expects($this->any())
            ->method('isDefault')->will($this->returnValue(true));

        $this->eavConfig->expects($this->any())
            ->method('getAttribute')
            ->with(
                ProductMetadataServiceInterface::ENTITY_TYPE,
                $attributeCode
            )->will($this->returnValue($this->attribute));

        $this->assertTrue($this->service->addOption($attributeCode, $option));
    }

    /**
     * Build label
     *
     * @param $labelText
     * @param $storeId
     * @return Label
     */
    private function buildLabel($labelText, $storeId)
    {
        $label = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Eav\Option\Label')
            ->disableOriginalConstructor()->getMock();

        $label->expects($this->any())
            ->method('getLabel')->will($this->returnValue($labelText));

        $label->expects($this->any())
            ->method('getStoreID')->will($this->returnValue($storeId));

        return $label;
    }

    /**
     * Test for remove attribute option
     */
    public function testRemoveOption()
    {
        $this->attributeId = 'test_attr';
        $optionId = 1;
        $this->eavConfig
            ->expects($this->once())
            ->method('getAttribute')
            ->with(ProductMetadataServiceInterface::ENTITY_TYPE, $this->attributeId)
            ->will($this->returnValue($this->attribute));
        $this->attribute->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->attribute->expects($this->once())->method('usesSource')->will($this->returnValue(true));
        $sourceMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Source\Table', [], [], '', false);
        $sourceMock
            ->expects($this->once())
            ->method('getOptionText')
            ->with($optionId)
            ->will($this->returnValue('option text'));
        $this->attribute->expects($this->any())->method('getSource')->will($this->returnValue($sourceMock));

        $this->attribute->expects($this->once())->method('save');
        $this->assertTrue($this->service->removeOption($this->attributeId, $optionId));
    }

    public function testRemoveOptionExceptionCase1()
    {
        $this->attributeId = 'test_attr';
        $optionId = 1;
        $this->eavConfig->expects($this->any())->method('getAttribute')->will($this->returnValue(false));
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            "No such entity with attribute_id = $this->attributeId"
        );
        $this->service->removeOption($this->attributeId, $optionId);

    }

    public function testRemoveOptionExceptionCase2()
    {
        $this->attributeId = 'test_attr';
        $optionId = 1;
        $this->eavConfig
            ->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($this->attribute));
        $this->attribute->expects($this->any())->method('getId')->will($this->returnValue(null));
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            "No such entity with attribute_id = $this->attributeId"
        );
        $this->service->removeOption($this->attributeId, $optionId);
    }

    public function testRemoveOptionExceptionCase3()
    {
        $this->attributeId = 'test_attr';
        $optionId = 1;
        $this->eavConfig
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue($this->attribute));
        $this->attribute->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->attribute->expects($this->any())->method('usesSource')->will($this->returnValue(false));
        $this->setExpectedException('Magento\Framework\Exception\StateException', 'Attribute doesn\'t have any option');
        $this->service->removeOption($this->attributeId, $optionId);
    }

    public function testRemoveOptionExceptionCase4()
    {
        $this->attributeId = 'test_attr';
        $optionId = 1;
        $this->eavConfig
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue($this->attribute));
        $this->attribute->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->attribute->expects($this->once())->method('usesSource')->will($this->returnValue(true));
        $sourceMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Source\Table', [], [], '', false);
        $sourceMock->expects($this->once())->method('getOptionText')->will($this->returnValue(false));
        $this->attribute->expects($this->any())->method('getSource')->will($this->returnValue($sourceMock));
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            sprintf('Attribute %s does not contain option with Id %s', $this->attributeId, $optionId));
        $this->service->removeOption($this->attributeId, $optionId);
    }
} 