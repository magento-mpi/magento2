<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_Order_Shipment_TrackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Model_Order_Shipment_Track
     */
    protected $_model;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_Sales_Model_Order_Shipment_Track');
    }

    public function testAddData()
    {
        $number = 123;
        $this->assertNull($this->_model->getTrackNumber());
        $this->_model->addData(array(
            'number' => $number,
            'test' => true
        ));

        $this->assertTrue($this->_model->getTest());
        $this->assertEquals($number, $this->_model->getTrackNumber());
    }

    public function testGetStoreId()
    {
        $storeId = 10;
        $storeObject = new Magento_Object(
            array('id' => $storeId)
        );

        $shipmentMock = $this->getMock('Mage_Sales_Model_Order_Shipment', array('getStore'), array(), '', false);
        $shipmentMock->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($storeObject));

        $this->_model->setShipment($shipmentMock);
        $this->assertEquals($storeId, $this->_model->getStoreId());
    }

    public function testSetGetNumber()
    {
        $this->assertNull($this->_model->getNumber());
        $this->assertNull($this->_model->getTrackNumber());

        $this->_model->setNumber('test');

        $this->assertEquals('test', $this->_model->getNumber());
        $this->assertEquals('test', $this->_model->getTrackNumber());
    }

    /**
     * @dataProvider isCustomDataProvider
     * @param bool $expectedResult
     * @param string $carrierCodeToSet
     */
    public function testIsCustom($expectedResult, $carrierCodeToSet)
    {
        $this->_model->setCarrierCode($carrierCodeToSet);
        $this->assertEquals($expectedResult, $this->_model->isCustom());
    }

    /**
     * @return array
     */
    public static function isCustomDataProvider()
    {
        return array(
            array(true, Mage_Sales_Model_Order_Shipment_Track::CUSTOM_CARRIER_CODE),
            array(false, 'ups'),
        );
    }
}
