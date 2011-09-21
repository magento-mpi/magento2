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

/**
 * @group module:Mage_Sales
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

    public function testSetGetNumber()
    {
        $this->assertNull($this->_model->getNumber());

        $this->_model->setNumber('test');
        $this->assertEquals('test', $this->_model->getNumber());
        $this->assertEquals('test', $this->_model->getTrackNumber());
    }
}
