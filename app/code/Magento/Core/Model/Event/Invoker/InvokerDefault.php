<?php
/**
 * Default event invoker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Event\Invoker;

class InvokerDefault implements \Magento\Core\Model\Event\InvokerInterface
{
    /**
     * Observer model factory
     *
     * @var \Magento\Core\Model\ObserverFactory
     */
    protected $_observerFactory;

    /**
     * Application state
     *
     * @var \Magento\Core\Model\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Core\Model\ObserverFactory $observerFactory
     * @param \Magento\Core\Model\App\State $appState
     */
    public function __construct(\Magento\Core\Model\ObserverFactory $observerFactory, \Magento\Core\Model\App\State $appState)
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
     * @return \Magento\Core\Model\Event\InvokerInterface
     * @throws \Magento\Core\Exception
     */
    protected function _callObserverMethod($object, $method, $observer)
    {
        if (method_exists($object, $method)) {
            $object->$method($observer);
        } elseif ($this->_appState->getMode() == \Magento\Core\Model\App\State::MODE_DEVELOPER) {
            \Mage::throwException('Method "' . $method . '" is not defined in "' . get_class($object) . '"');
        }
        return $this;
    }
}
