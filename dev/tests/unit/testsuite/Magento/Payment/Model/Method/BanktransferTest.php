<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Payment_Model_Method_BanktransferTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Payment_Model_Method_Banktransfer
     */
    protected $_object;

    protected function setUp()
    {
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $paymentDataMock = $this->getMock('Magento_Payment_Helper_Data', array(), array(), '', false);
        $this->_object = new Magento_Payment_Model_Method_Banktransfer($eventManager, $paymentDataMock);
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals('Magento_Payment_Block_Info_Instructions', $this->_object->getInfoBlockType());
    }
}
