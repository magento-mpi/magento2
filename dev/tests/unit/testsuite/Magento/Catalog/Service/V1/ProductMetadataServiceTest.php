<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder;
use Magento\Catalog\Service\V1\Data\Eav\OptionBuilder;
use Magento\Catalog\Service\V1\Data\Eav\ValidationRuleBuilder;

class ProductMetadataServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getAttributeMetadata
     */
    public function testGetAttributeMetadata()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $data = array(
            'attribute_id' => 1,
            'attribute_code' => 'description',
            'frontend_label' => 'English',
            'store_labels' => array(1 => 'France'),
            'frontend_input' => 'textarea',
        );

        //attributeMock
        $attributeMock = $this->getMock(
            'Magento\Framework\Object',
            array('usesSource', 'getSource', 'isScopeGlobal', 'isSystem'),
            array('data' => $data)
        );
        $attributeMock->expects($this->any())->method('isScopeGlobal')->will($this->returnValue(true));
        $attributeMock->expects($this->any())->method('isSystem')->will($this->returnValue(true));
        $attributeMock->expects($this->any())->method('usesSource')->will($this->returnValue(true));
        $attributeMock->expects($this->any())->method('getSource')
            ->will($this->returnValue(new \Magento\Framework\Object()));

        // eavConfigMock
        $eavConfigMock = $this->getMock('Magento\Eav\Model\Config', array('getAttribute'), array(), '', false);
        $eavConfigMock->expects($this->any())->method('getAttribute')->will($this->returnValue($attributeMock));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $validationRuleBuilder = $helper->getObject('\Magento\Catalog\Service\V1\Data\Eav\ValidationRuleBuilder');
        $optionBuilder = $helper->getObject('\Magento\Catalog\Service\V1\Data\Eav\OptionBuilder');
        $attrMetadataBuilder = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder',
            [
                'optionBuilder' => $optionBuilder,
                'validationRuleBuilder' => $validationRuleBuilder
            ]
        );

        // create service
        $service = $objectManager->getObject('Magento\Catalog\Service\V1\ProductMetadataService',
            array(
                'eavConfig' => $eavConfigMock,
                'attributeMetadataBuilder'
                    => $attrMetadataBuilder
            )
        );

        $dto = $service->getAttributeMetadata('entity_type', 'attr_code');
        $this->assertInstanceOf('Magento\Framework\Service\Data\AbstractObject', $dto);
        $this->assertEquals($attributeMock->getFrontendInput(), $dto->getFrontendInput());

        $this->assertTrue(is_array($dto->getFrontendLabel()));
        $this->assertArrayHasKey('store_id', $dto->getFrontendLabel()[0]);
    }
}
