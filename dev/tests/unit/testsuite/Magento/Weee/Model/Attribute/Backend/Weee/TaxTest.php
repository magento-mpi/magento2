<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Test class for \Magento\Weee\Model\Attribute\Backend\Weee\Tax
 */
namespace Magento\Weee\Model\Attribute\Backend\Weee;

use Magento\TestFramework\Helper\ObjectManager;

class TaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Weee\Model\Attribute\Backend\Weee\Tax
     */
    protected $model;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->model = $this->objectManager->getObject('Magento\Weee\Model\Attribute\Backend\Weee\Tax');
    }

    public function testGetBackendModelName()
    {
        $this->assertEquals('Magento\Weee\Model\Attribute\Backend\Weee\Tax', $this->model->getBackendModelName());
    }

    public function testValidate()
    {
        $attributeMock = $this->getMockBuilder('Magento\Eav\Model\Attribute')
            ->setMethods(['getName'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('weeeTax'));

        $modelMock = $this->getMockBuilder('Magento\Weee\Model\Attribute\Backend\Weee\Tax')
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $modelMock
            ->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($attributeMock));

        $taxes = array(array('state' => 'Texas', 'country' => 'US', 'website_id' => '1'));
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($taxes));

        // No exception
        $modelMock->validate($productMock);

        $taxes = array(array('state' => 'Texas', 'country' => 'US', 'website_id' => '1'),
            array('state' => 'Texas', 'country' => 'US', 'website_id' => '1'));
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($taxes));

        // Exception caught
        $this->setExpectedException('Exception', 'We found a duplicate of website, country and state fields for a fixed product tax');
        $modelMock->validate($productMock);
    }

    public function testAfterLoad()
    {
        $data = array(array('website_id' => 1, 'value' => 1));

        $attributeTaxMock = $this->getMockBuilder('Magento\Weee\Model\Resource\Attribute\Backend\Weee\Tax')
            ->setMethods(['loadProductData'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeTaxMock
            ->expects($this->any())
            ->method('loadProductData')
            ->will($this->returnValue($data));

        $attributeMock = $this->getMockBuilder('Magento\Eav\Model\Attribute')
            ->setMethods(['getName'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('weeeTax'));

        $model = $this->objectManager->getObject('Magento\Weee\Model\Attribute\Backend\Weee\Tax',
            [
                'attributeTax' => $attributeTaxMock,
                '_attribute' => $attributeMock
            ]
        );

        $model->setAttribute($attributeMock);
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $model->afterLoad($productMock);
    }

    public function testAfterSave1()
    {
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getOrigData', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();

        $taxes1 = array(array('state' => 'TX', 'country' => 'US', 'website_id' => '1'));
        $taxes2 = array(array('state' => 'TX', 'country' => 'US', 'website_id' => '2', 'price' => 100));
        $productMock
            ->expects($this->once())
            ->method('getOrigData')
            ->will($this->returnValue($taxes1));
        $productMock
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($taxes2));

        $data = array('state' => 'TX', 'country' => 'US', 'website_id' => '2', 'value' => 100, 'attribute_id' => 1);

        $attributeTaxMock = $this->getMockBuilder('Magento\Weee\Model\Resource\Attribute\Backend\Weee\Tax')
            ->setMethods(['deleteProductData', 'insertProductData'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeTaxMock
            ->expects($this->once())
            ->method('deleteProductData')
            ->will($this->returnValue(null));
        $attributeTaxMock
            ->expects($this->once())
            ->method('insertProductData')
            ->with($productMock, $data)
            ->will($this->returnValue(null));

        $attributeMock = $this->getMockBuilder('Magento\Eav\Model\Attribute')
            ->setMethods(['getName', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('weeeTax'));
        $attributeMock
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $model = $this->objectManager->getObject('Magento\Weee\Model\Attribute\Backend\Weee\Tax',
            [
                'attributeTax' => $attributeTaxMock,
                '_attribute' => $attributeMock
            ]
        );

        $model->setAttribute($attributeMock);
        $model->afterSave($productMock);
    }

    public function testAfterSave2()
    {
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getOrigData', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();

        $taxes1 = array(array('state' => '0', 'country' => 'US', 'website_id' => '1'));
        $taxes2 = array(array('state' => '0', 'country' => 'US', 'website_id' => '2', 'price' => 100));
        $productMock
            ->expects($this->once())
            ->method('getOrigData')
            ->will($this->returnValue($taxes1));
        $productMock
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($taxes2));

        $data = array('state' => '0', 'country' => 'US', 'website_id' => '2', 'value' => 100, 'attribute_id' => 1);

        $attributeTaxMock = $this->getMockBuilder('Magento\Weee\Model\Resource\Attribute\Backend\Weee\Tax')
            ->setMethods(['deleteProductData', 'insertProductData'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeTaxMock
            ->expects($this->once())
            ->method('deleteProductData')
            ->will($this->returnValue(null));
        $attributeTaxMock
            ->expects($this->once())
            ->method('insertProductData')
            ->with($productMock, $data)
            ->will($this->returnValue(null));

        $attributeMock = $this->getMockBuilder('Magento\Eav\Model\Attribute')
            ->setMethods(['getName', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('weeeTax'));
        $attributeMock
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $model = $this->objectManager->getObject('Magento\Weee\Model\Attribute\Backend\Weee\Tax',
            [
                'attributeTax' => $attributeTaxMock,
                '_attribute' => $attributeMock
            ]
        );

        $model->setAttribute($attributeMock);
        $model->afterSave($productMock);
    }

    public function testAfterDelete()
    {
        $attributeTaxMock = $this->getMockBuilder('Magento\Weee\Model\Resource\Attribute\Backend\Weee\Tax')
            ->setMethods(['deleteProductData'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeTaxMock
            ->expects($this->once())
            ->method('deleteProductData')
            ->with(null, null)
            ->will($this->returnValue(null));

        $model = $this->objectManager->getObject('Magento\Weee\Model\Attribute\Backend\Weee\Tax',
            [
                'attributeTax' => $attributeTaxMock,
            ]
        );

        $model->afterDelete(null);
    }

    public function testGetTable()
    {
        $attributeTaxMock = $this->getMockBuilder('Magento\Weee\Model\Resource\Attribute\Backend\Weee\Tax')
            ->setMethods(['getTable'])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeTaxMock
            ->expects($this->once())
            ->method('getTable')
            ->with('weee_tax')
            ->will($this->returnValue(null));

        $model = $this->objectManager->getObject('Magento\Weee\Model\Attribute\Backend\Weee\Tax',
            [
                'attributeTax' => $attributeTaxMock,
            ]
        );

        $model->getTable();
    }
}
