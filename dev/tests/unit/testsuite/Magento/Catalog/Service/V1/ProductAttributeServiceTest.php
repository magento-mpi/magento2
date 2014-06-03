<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;

class ProductAttributeServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test for retrieving attribute options
     */
    public function testOptions()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $attributeCode = 'attr_code';
        $metadataServiceMock = $this->getMock(
            'Magento\Catalog\Service\V1\ProductMetadataService', array('getAttributeMetadata'),
            array(),
            '',
            false
        );

        $mock = $this->getMock(
            'Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata', array('getOptions'),
            array(),
            '',
            false
        );

        $mock->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(array()));

        $metadataServiceMock->expects($this->once())
            ->method('getAttributeMetadata')
            ->with(
                ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
                $attributeCode
            )
        ->will($this->returnValue($mock));



        /** @var \Magento\Catalog\Service\V1\ProductAttributeServiceInterface $service */
        $service = $objectManager->getObject(
            'Magento\Catalog\Service\V1\ProductAttributeService',
            array(
                'metadataService' => $metadataServiceMock
            )
        );
        $service->options($attributeCode);
    }
}
