<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Service\V1\Data\ProductAttributeTypeBuilder;
use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;

class ProductAttributeReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for retrieving product attributes types
     */
    public function testTypes()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $inputtypeFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory',
            array('create')
        );
        $inputtypeFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue(
                $objectManager->getObject('Magento\Catalog\Model\Product\Attribute\Source\Inputtype')
            ));

        $productAttributeReadService = new ProductAttributeReadService(
            $objectManager->getObject('Magento\Catalog\Service\V1\ProductMetadataService'),
            $inputtypeFactoryMock,
            new ProductAttributeTypeBuilder()
        );
        $types = $productAttributeReadService->types();
        $this->assertTrue(is_array($types));
        $this->assertNotEmpty($types);
        $this->assertInstanceOf('Magento\Catalog\Service\V1\Data\ProductAttributeType', current($types));
    }

    /**
     * Test for retrieving product attribute
     */
    public function testInfo()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $attributeCode = 'attr_code';
        $metadataServiceMock = $this->getMock(
            'Magento\Catalog\Service\V1\ProductMetadataService', array('getAttributeMetadata'),
            array(),
            '',
            false
        );
        $metadataServiceMock->expects($this->once())
            ->method('getAttributeMetadata')
            ->with(
                ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
                $attributeCode
            );

        /** @var \Magento\Catalog\Service\V1\ProductAttributeReadServiceInterface $service */
        $service = $objectManager->getObject(
            'Magento\Catalog\Service\V1\ProductAttributeReadService',
            array(
               'metadataService' => $metadataServiceMock
            )
        );
        $service->info($attributeCode);
    }
}
