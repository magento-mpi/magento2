<?php
/**
 * Default event invoker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Event_Invoker_InvokerDefault implements Magento_Core_Model_Event_InvokerInterface
{
    /**
     * Observer model factory
     *
     * @var Magento_Core_Model_ObserverFactory
     */
    protected $_observerFactory;

    /**
     * Application state
     *
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Model_ObserverFactory $observerFactory
     * @param Magento_Core_Model_App_State $appState
     */
    public function __construct(Magento_Core_Model_ObserverFactory $observerFactory, Magento_Core_Model_App_State $appState)
    {
        $this->_observerFactory = $observerFactory;
        $this->_appState = $appState;
    }

    /**
     * Dispatch event
     *
     * @param array $configuration
     * @param \Magento\Event\Observer $observer
     */
    public function dispatch(array $configuration, \Magento\Event\Observer $observer)
    {
        /** Check whether event observer is disabled */
        if (isset($configuration['disabled']) && true === $configuration['disabled']) {
            return;
        }

        if (isset($configuration['shared']) && false === $configuration['shared']) {
            $object = $this->_observerFactory->create($configuration['instance']);
        } else {
            $object = $this->_observerFactory->get($configuration['instance']);
        }
        $this->_callObserverMethod($object, $configuration['method'], $observer);
    }

    /**
     * Performs non-existent observer method calls protection
     *
     * @param object $object
     * @param string $method
     * @param \Magento\Event\Observer $observer
     * @return Magento_Core_Model_Event_InvokerInterface
     * @throws Magento_Core_Exception
     */
    protected function _callObserverMethod($object, $method, $observer)
    {
        if (method_exists($object, $method)) {
            $object->$method($observer);
        } elseif ($this->_appState->getMode() == Magento_Core_Model_App_State::MODE_DEVELOPER) {
            Mage::throwException('Method "' . $method . '" is not defined in "' . get_class($object) . '"');
        }
        return $this;
    }
}
