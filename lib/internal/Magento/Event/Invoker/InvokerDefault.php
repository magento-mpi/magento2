<?php
/**
 * Default event invoker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Event\Invoker;

use Zend\Stdlib\Exception\LogicException;
use Magento\Event\Observer;

class InvokerDefault implements \Magento\Event\InvokerInterface
{
    /**
     * Observer model factory
     *
     * @var \Magento\Event\ObserverFactory
     */
    protected $_observerFactory;

    /**
     * Application state
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Event\ObserverFactory $observerFactory
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(\Magento\Event\ObserverFactory $observerFactory, \Magento\Framework\App\State $appState)
    {
        $this->_observerFactory = $observerFactory;
        $this->_appState = $appState;
    }

    /**
     * Dispatch event
     *
     * @param array $configuration
     * @param Observer $observer
     * @return void
     */
    public function dispatch(array $configuration, Observer $observer)
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
     * @param Observer $observer
     * @return $this
     * @throws \LogicException
     */
    protected function _callObserverMethod($object, $method, $observer)
    {
        if (method_exists($object, $method)) {
            $object->{$method}($observer);
        } elseif ($this->_appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER) {
            throw new \LogicException('Method "' . $method . '" is not defined in "' . get_class($object) . '"');
        }
        return $this;
    }
}
