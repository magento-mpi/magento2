<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

use Magento\Catalog\Service\V1\Data\ProductBuilder;
use Magento\Catalog\Service\V1\Data\Product as ProductDataObject;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProductBuilder
     */
    protected $productBuilder;

    /**
     * @var Converter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converter;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->productBuilder = $objectManager->getObject('Magento\Catalog\Service\V1\Data\ProductBuilder');
    }

    public function testCreateProductDataFromModel()
    {
        $productModelMock = $this->getMockBuilder('\Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $attributes = [];
        $attrCodes = ['sku', 'price', 'status', 'updatedAt', 'entity_id'];
        foreach($attrCodes as $code) {
            $attributeMock = $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
                ->disableOriginalConstructor()
                ->getMock();
            $attributeMock->expects($this->once())->method('getAttributeCode')->will($this->returnValue($code));
            $attributes[] = $attributeMock;
        }
        $productModelMock->expects($this->once())->method('getAttributes')->will($this->returnValue($attributes));

        $dataUsingMethodCallback = $this->returnCallback(
            function ($attrCode) {
                if(in_array($attrCode, ['sku', 'price', 'entity_id'])) {
                    return $attrCode . 'value';
                }
                return null;
            }
        );
        $productModelMock->expects($this->exactly(count($attrCodes)))
            ->method('getDataUsingMethod')
            ->will($dataUsingMethodCallback);

        $dataCallback = $this->returnCallback(
            function ($attrCode) {
                if($attrCode == 'status') {
                    return $attrCode . 'dataValue';
                }
                return null;
            }
        );
        $productModelMock->expects($this->exactly(2))
            ->method('getData')
            ->will($dataCallback);

        $this->converter = new Converter($this->productBuilder);
        $productData = $this->converter->createProductDataFromModel($productModelMock);
        $this->assertEquals(ProductDataObject::SKU . 'value', $productData->getSku());
        $this->assertEquals('entity_id' . 'value', $productData->getId());
        $this->assertEquals(ProductDataObject::PRICE . 'value', $productData->getPrice());
        $this->assertEquals(ProductDataObject::STATUS . 'dataValue', $productData->getStatus());
        $this->assertEquals(null, $productData->getUpdatedAt());
    }
}
