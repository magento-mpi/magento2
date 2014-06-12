<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for retrieving product attributes types
     */
    public function testTypes()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $inputTypeFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory',
            array('create')
        );
        $inputTypeFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue(
                $objectManager->getObject('Magento\Catalog\Model\Product\Attribute\Source\Inputtype')
            ));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $attributeTypeBuilder = $helper->getObject('\Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\TypeBuilder');
        $productAttributeReadService = $objectManager->getObject(
            '\Magento\Catalog\Service\V1\Product\Attribute\ReadService',
            [
                'metadataService' => $objectManager->getObject('Magento\Catalog\Service\V1\ProductMetadataService'),
                'inputTypeFactory' => $inputTypeFactoryMock,
                'attributeTypeBuilder' => $attributeTypeBuilder
            ]
        );
        $types = $productAttributeReadService->types();
        $this->assertTrue(is_array($types));
        $this->assertNotEmpty($types);
        $this->assertInstanceOf('Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\Type', current($types));
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

        /** @var \Magento\Catalog\Service\V1\Product\Attribute\ReadServiceInterface $service */
        $service = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\Attribute\ReadService',
            array(
               'metadataService' => $metadataServiceMock
            )
        );
        $service->info($attributeCode);
    }
}
