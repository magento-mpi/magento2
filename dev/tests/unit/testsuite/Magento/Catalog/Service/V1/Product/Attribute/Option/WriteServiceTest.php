<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\Catalog\Service\V1\Data\Eav\Option\Label;
use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;
use Magento\TestFramework\Helper\ObjectManager;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for retrieving attribute options
     */
    public function testAddOption()
    {
        $objectManager = new ObjectManager($this);

        $attributeCode = 'attr_code';

        $label = $this->buildLabel('st 42', 42);

        $attribute = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
            ->disableOriginalConstructor()->getMock();
        $attribute->expects($this->any())
            ->method('usesSource')
            ->will($this->returnValue(true));

        $attribute->expects($this->at(1))
            ->method('__call')
            ->with('setDefault', [['new_option']]);

        $attribute->expects($this->at(2))
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

        $attribute->expects($this->any())
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

        $eavConfig = $this->getMockBuilder('Magento\Eav\Model\Config')
            ->disableOriginalConstructor()->getMock();

        $eavConfig->expects($this->any())
            ->method('getAttribute')
            ->with(
                ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
                $attributeCode
            )->will($this->returnValue($attribute));

        /** @var \Magento\Catalog\Service\V1\Product\Attribute\Option\WriteServiceInterface $service */
        $service = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\Attribute\Option\WriteService',
            array(
                'eavConfig' => $eavConfig
            )
        );

        $this->assertTrue($service->addOption($attributeCode, $option));
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
} 