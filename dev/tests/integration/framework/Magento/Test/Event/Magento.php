<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Observer of Magento events triggered using Mage::dispatchEvent()
 */
class Magento_Test_Event_Magento
{
    /**
     * Used when Magento framework instantiates the class on its own and passes nothing to the constructor
     *
     * @var Magento_Test_EventManager
     */
    protected static $_defaultEventManager;

    /**
     * @var Magento_Test_EventManager
     */
    protected $_eventManager;

    /**
     * Assign default event manager instance
     *
     * @param Magento_Test_EventManager $eventManager
     */
    public static function setDefaultEventManager(Magento_Test_EventManager $eventManager = null)
    {
        self::$_defaultEventManager = $eventManager;
    }

    /**
     * Constructor
     *
     * @param Magento_Test_EventManager $eventManager
     * @throws Magento_Exception
     */
    public function __construct($eventManager = null)
    {
        $this->_eventManager = $eventManager ?: self::$_defaultEventManager;
        if (!($this->_eventManager instanceof Magento_Test_EventManager)) {
            throw new Magento_Exception('Instance of the "Magento_Test_EventManager" is expected.');
        }
    }

    /**
     * Handler for 'controller_front_init_before' event, that converts it into 'initFrontControllerBefore'
     */
    public function initFrontControllerBefore()
    {
        $this->_eventManager->fireEvent('initFrontControllerBefore');
    }
}
