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
class Magento_TestFramework_Event_Magento
{
    /**
     * Used when Magento framework instantiates the class on its own and passes nothing to the constructor
     *
     * @var Magento_TestFramework_EventManager
     */
    protected static $_defaultEventManager;

    /**
     * @var Magento_TestFramework_EventManager
     */
    protected $_eventManager;

    /**
     * Assign default event manager instance
     *
     * @param Magento_TestFramework_EventManager $eventManager
     */
    public static function setDefaultEventManager(Magento_TestFramework_EventManager $eventManager = null)
    {
        self::$_defaultEventManager = $eventManager;
    }

    /**
     * Constructor
     *
     * @param Magento_TestFramework_EventManager $eventManager
     * @throws \Magento\MagentoException
     */
    public function __construct($eventManager = null)
    {
        $this->_eventManager = $eventManager ?: self::$_defaultEventManager;
        if (!($this->_eventManager instanceof Magento_TestFramework_EventManager)) {
            throw new \Magento\MagentoException('Instance of the "Magento_TestFramework_EventManager" is expected.');
        }
    }

    /**
     * Handler for 'core_app_init_current_store_after' event, that converts it into 'initStoreAfter'
     */
    public function initStoreAfter()
    {
        $this->_eventManager->fireEvent('initStoreAfter');
    }
}
