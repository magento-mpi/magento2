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

class Magento_Payment_Model_Method_CashondeliveryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Payment_Model_Method_Cashondelivery
     */
    protected $_object;

    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $paymentDataMock = $this->getMock('Magento_Payment_Helper_Data', array(), array(), '', false);
        $adapterFactoryMock = $this->getMock('Magento_Core_Model_Log_AdapterFactory', array('create'),
            array(), '', false);

        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $this->_object = $helper->getObject('Magento_Payment_Model_Method_Cashondelivery', array(
            'eventManager' => $eventManager,
            'paymentData' => $paymentDataMock,
            'coreStoreConfig' => $coreStoreConfig,
            'logAdapterFactory' => $adapterFactoryMock,
        ));
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals('Magento_Payment_Block_Info_Instructions', $this->_object->getInfoBlockType());
    }
}
