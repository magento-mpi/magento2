<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_GiftCard_Model_Observer
     */
    protected $_model;

    /**
     * Test that dependency injections passed to the constructor will not be duplicated in _data property
     */
    public function testConstructorValidArguments()
    {
        $appState = $this->getMock('Magento_Core_Model_App_State', array(), array(), '', false);
        $storeManager = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $context = new Magento_Core_Model_Context(
            $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false),
            $this->getMock('Magento_Core_Model_Cache', array(), array(), '', false),
            $appState,
            $storeManager
        );
        $coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);
        $this->_model = new Magento_GiftCard_Model_Observer(
            $this->getMock('Magento_GiftCard_Helper_Data', array(), array(), '', false),
            $context,
            $coreRegistry,
            null,
            null,
            array(
            'email_template_model' => $this->getMock('Magento_Core_Model_Email_Template', array(), array(), '', false),
            'custom_field'         => 'custom_value',
        ));
        $this->assertEquals(array('custom_field' => 'custom_value'), $this->_model->getData());
    }

    /**
     * Test that only valid model instance can be passed to the constructor
     *
     * @expectedException InvalidArgumentException
     */
    public function testConstructorInvalidArgument()
    {
        $appState = $this->getMock('Magento_Core_Model_App_State', array(), array(), '', false);
        $storeManager = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $context = new Magento_Core_Model_Context(
            $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false),
            $this->getMock('Magento_Core_Model_CacheInterface', array(), array(), '', false),
            $appState,
            $storeManager
        );
        $this->_model = new Magento_GiftCard_Model_Observer(
            $this->getMock('Magento_GiftCard_Helper_Data', array(), array(), '', false),
            $context,
            $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false),
            null,
            null,
            array('email_template_model' => new stdClass())
        );
    }
}
