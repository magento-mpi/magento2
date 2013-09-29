<?php
/**
 * Event manager
 * Used to dispatch global events
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Event;

class Manager
{
    /**
     * Events cache
     *
     * @var array
     */
    protected $_events = array();

    /**
     * Event invoker
     *
     * @var \Magento\Core\Model\Event\InvokerInterface
     */
    protected $_invoker;

    /**
     * Event config
     *
     * @var \Magento\Core\Model\Event\ConfigInterface
     */
    protected $_eventConfig;

    /**
     * Magento event factory
     *
     * @var \Magento\EventFactory
     */
    protected $_eventFactory;

    /**
     * Magento event observer factory
     *
     * @var \Magento\Event\ObserverFactory
     */
    protected $_eventObserverFactory;

    /**
     * @param \Magento\Core\Model\Event\InvokerInterface $invoker
     * @param \Magento\Core\Model\Event\ConfigInterface $eventConfig
     * @param \Magento\EventFactory $eventFactory
     * @param \Magento\Event\ObserverFactory $eventObserverFactory
     */
    public function __construct(
        \Magento\Core\Model\Event\InvokerInterface $invoker,
        \Magento\Core\Model\Event\ConfigInterface $eventConfig,
        \Magento\EventFactory $eventFactory,
        \Magento\Event\ObserverFactory $eventObserverFactory
    ) {
        $this->_invoker = $invoker;
        $this->_eventConfig = $eventConfig;
        $this->_eventFactory = $eventFactory;
        $this->_eventObserverFactory = $eventObserverFactory;
    }

    /**
     * Dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiple observers matching event name pattern
     *
     * @param string $eventName
     * @param array $data
     */
    public function dispatch($eventName, array $data = array())
    {
        \Magento\Profiler::start('EVENT:' . $eventName, array('group' => 'EVENT', 'name' => $eventName));
        foreach ($this->_eventConfig->getObservers($eventName) as $observerConfig) {
            /** @var $event \Magento\Event */
            $event = $this->_eventFactory->create(array('data' => $data));
            $event->setName($eventName);

            /** @var $observer \Magento\Event\Observer */
            $observer = $this->_eventObserverFactory->create();
            $observer->setData(array_merge(array('event' => $event), $data));

            \Magento\Profiler::start('OBSERVER:' . $observerConfig['name']);
            $this->_invoker->dispatch($observerConfig, $observer);
            \Magento\Profiler::stop('OBSERVER:' .  $observerConfig['name']);
        }
        \Magento\Profiler::stop('EVENT:' . $eventName);
    }
}
