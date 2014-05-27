<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Catalog\Service\V1\ProductAttributeReadService;
use Magento\Catalog\Service\V1\ProductMetadataService;

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
            $inputtypeFactoryMock
        );
        $types = $productAttributeReadService->types();
        $this->assertTrue(is_array($types));
        $this->assertGreaterThan(0, count($types));
        $this->assertArrayHasKey('value', $types[0]);
        $this->assertArrayHasKey('label', $types[0]);
    }
}
