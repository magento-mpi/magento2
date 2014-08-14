<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

class MetadataServiceTest extends \PHPUnit_Framework_TestCase
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
            array('usesSource', 'getSource', 'isScopeGlobal'),
            array('data' => $data)
        );
        $attributeMock->expects($this->any())->method('isScopeGlobal')->will($this->returnValue(true));
        $attributeMock->expects($this->any())->method('usesSource')->will($this->returnValue(true));
        $attributeMock->expects($this->any())->method('getSource')
            ->will($this->returnValue(new \Magento\Framework\Object()));

        // eavConfigMock
        $eavConfigMock = $this->getMock('Magento\Eav\Model\Config', array('getAttribute'), array(), '', false);
        $eavConfigMock->expects($this->any())->method('getAttribute')->will($this->returnValue($attributeMock));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $validationRuleBuilder = $helper->getObject('\Magento\Catalog\Service\V1\Data\Eav\ValidationRuleBuilder');
        $optionBuilder = $helper->getObject('\Magento\Catalog\Service\V1\Data\Eav\OptionBuilder');
        $frontendLabelBuilder = $helper
            ->getObject('\Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\FrontendLabelBuilder');
        /** @var \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder $attrMetadataBuilder */
        $attrMetadataBuilder = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder',
            [
                'optionBuilder' => $optionBuilder,
                'validationRuleBuilder' => $validationRuleBuilder,
                'frontendLabelBuilder' => $frontendLabelBuilder,
            ]
        );

        // create service
        /** @var \Magento\Catalog\Service\V1\MetadataService $service */
        $service = $objectManager->getObject(
            'Magento\Catalog\Service\V1\MetadataService',
            array(
                'eavConfig' => $eavConfigMock,
                'attributeMetadataBuilder'
                => $attrMetadataBuilder
            )
        );

        $dto = $service->getAttributeMetadata('entity_type', 'attr_code');
        $this->assertInstanceOf('Magento\Framework\Service\Data\AbstractObject', $dto);
        $this->assertEquals($attributeMock->getFrontendInput(), $dto->getFrontendInput());

        $this->assertEquals(0, $dto->getFrontendLabel()[0]->getStoreId());
        $this->assertEquals(1, $dto->getFrontendLabel()[1]->getStoreId());
        $this->assertEquals('English', $dto->getFrontendLabel()[0]->getLabel());
        $this->assertEquals('France', $dto->getFrontendLabel()[1]->getLabel());
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetAttributeMetadataNoSuchEntityException()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        // eavConfigMock
        $eavConfigMock = $this->getMock('Magento\Eav\Model\Config', array('getAttribute'), array(), '', false);
        $eavConfigMock->expects($this->any())->method('getAttribute')->will($this->returnValue(null));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $validationRuleBuilder = $helper->getObject('\Magento\Catalog\Service\V1\Data\Eav\ValidationRuleBuilder');
        $optionBuilder = $helper->getObject('\Magento\Catalog\Service\V1\Data\Eav\OptionBuilder');
        $frontendLabelBuilder = $helper
            ->getObject('\Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\FrontendLabelBuilder');
        /** @var \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder $attrMetadataBuilder */
        $attrMetadataBuilder = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder',
            [
                'optionBuilder' => $optionBuilder,
                'validationRuleBuilder' => $validationRuleBuilder,
                'frontendLabelBuilder' => $frontendLabelBuilder,
            ]
        );

        // create service
        /** @var \Magento\Catalog\Service\V1\MetadataService $service */
        $service = $objectManager->getObject(
            'Magento\Catalog\Service\V1\MetadataService',
            array(
                'eavConfig' => $eavConfigMock,
                'attributeMetadataBuilder'
                => $attrMetadataBuilder
            )
        );

        $service->getAttributeMetadata('entity_type', 'attr_code');
    }
}
