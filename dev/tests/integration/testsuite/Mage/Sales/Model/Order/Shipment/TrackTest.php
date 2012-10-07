<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sale
 * @subpackage  integration_tests
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
        $this->_model = new Mage_Sales_Model_Order_Shipment_Track();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testSetGetNumber()
    {
        $this->assertNull($this->_model->getNumber());

        $this->_model->setNumber('test');
        $this->assertEquals('test', $this->_model->getNumber());
        $this->assertEquals('test', $this->_model->getTrackNumber());
    }

    public function testIsCustom()
    {
        $this->_model->setCarrierCode('ups');
        $this->assertFalse($this->_model->isCustom());
        $this->_model->setCarrierCode('custom');
        $this->assertTrue($this->_model->isCustom());
    }
}
