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
        $this->_model = $this->getMock('Mage_Sales_Model_Order_Shipment_Track', null, array(), '', false);
    }

    public function testAddData()
    {
        $number = 123;

        $this->_model->addData(array(
            'number' => $number,
            'test' => true
        ));

        $this->assertTrue($this->_model->getTest());
        $this->assertEquals($number, $this->_model->getTrackNumber());
    }
}
