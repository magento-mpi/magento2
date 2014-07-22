<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

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
        $this->productBuilder = $this->getMock(
            'Magento\Catalog\Service\V1\Data\ProductBuilder',
            [],
            [],
            '',
            false
        );
    }

    public function testCreateProductDataFromModel()
    {
        $productModelMock = $this->getMockBuilder('\Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $attrMock = $this->getMockBuilder('\Magento\Catalog\Model\Resource\Eav\Attribute')
            ->disableOriginalConstructor()
            ->getMock();

        $attrMock->expects($this->at(0))->method('getAttributeCode')->will($this->returnValue('sku'));
        $attrMock->expects($this->at(1))->method('getAttributeCode')->will($this->returnValue('price'));
        $attrMock->expects($this->at(2))->method('getAttributeCode')->will($this->returnValue('status'));
        $attrMock->expects($this->at(3))->method('getAttributeCode')->will($this->returnValue('updatedAt'));
        $attrMock->expects($this->at(4))->method('getAttributeCode')->will($this->returnValue('entity_id'));
        $attrMock->expects($this->at(5))->method('getAttributeCode')->will($this->returnValue('store_id'));

        $attrList = [$attrMock, $attrMock, $attrMock, $attrMock, $attrMock, $attrMock];

        $productModelMock->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue($attrList));

        $attributes = [
            ProductDataObject::SKU => ProductDataObject::SKU . 'value',
            ProductDataObject::PRICE => ProductDataObject::PRICE . 'value',
            ProductDataObject::STATUS => ProductDataObject::STATUS . 'dataValue',
            ProductDataObject::STORE_ID => ProductDataObject::STORE_ID . 'value'
        ];
        $this->productBuilder->expects($this->once())
            ->method('populateWithArray')
            ->with($attributes);

        $this->productBuilder->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($attributes));

        $this->productBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new ProductDataObject($this->productBuilder)));

        $dataUsingMethodCallback = $this->returnCallback(
            function ($attrCode) {
                if (in_array($attrCode, ['sku', 'price', 'entity_id'])) {
                    return $attrCode . 'value';
                }
                return null;
            }
        );
        $productModelMock->expects($this->exactly(count($attrList)))
            ->method('getDataUsingMethod')
            ->will($dataUsingMethodCallback);

        $productModelMock->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue(ProductDataObject::STORE_ID . 'value'));

        $dataCallback = $this->returnCallback(
            function ($attrCode) {
                if ($attrCode == 'status') {
                    return $attrCode . 'dataValue';
                }
                return null;
            }
        );
        $productModelMock->expects($this->exactly(3))
            ->method('getData')
            ->will($dataCallback);

        $this->converter = new Converter($this->productBuilder);
        $productData = $this->converter->createProductDataFromModel($productModelMock);
        $this->assertEquals(ProductDataObject::SKU . 'value', $productData->getSku());
        $this->assertEquals(ProductDataObject::PRICE . 'value', $productData->getPrice());
        $this->assertEquals(ProductDataObject::STATUS . 'dataValue', $productData->getStatus());
        $this->assertEquals(ProductDataObject::STORE_ID . 'value', $productData->getStoreId());
        $this->assertEquals(null, $productData->getUpdatedAt());
    }
}
