<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Catalog\Service\V1\ProductMetadataService;

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

        // create service
        $service = $objectManager->getObject('Magento\Catalog\Service\V1\ProductMetadataService',
            array(
                'eavConfig' => $eavConfigMock,
                'attributeMetadataBuilder'
                    => new AttributeMetadataBuilder(new OptionBuilder(), new ValidationRuleBuilder())
            )
        );

        $dto = $service->getAttributeMetadata('entity_type', 'attr_code');
        $this->assertInstanceOf('Magento\Framework\Service\Data\AbstractObject', $dto);
        $this->assertEquals($attributeMock->getFrontendInput(), $dto->getFrontendInput());

        $this->assertTrue(is_array($dto->getFrontendLabel()));
        $this->assertArrayHasKey('store_id', $dto->getFrontendLabel()[0]);
    }
}
