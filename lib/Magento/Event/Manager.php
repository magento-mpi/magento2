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
namespace Magento\Event;

class Manager implements ManagerInterface
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
     * @var \Magento\Event\InvokerInterface
     */
    protected $_invoker;

    /**
     * Event config
     *
     * @var \Magento\Event\ConfigInterface
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
    protected $wrapperFactory;

    /**
     * @param InvokerInterface $invoker
     * @param ConfigInterface $eventConfig
     * @param \Magento\EventFactory $eventFactory
     * @param WrapperFactory $wrapperFactory
     */
    public function __construct(
        \Magento\Event\InvokerInterface $invoker,
        \Magento\Event\ConfigInterface $eventConfig,
        \Magento\EventFactory $eventFactory,
        \Magento\Event\WrapperFactory $wrapperFactory
    ) {
        $this->_invoker = $invoker;
        $this->_eventConfig = $eventConfig;
        $this->_eventFactory = $eventFactory;
        $this->wrapperFactory = $wrapperFactory;
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

            /** @var $wrapper \Magento\Event\Observer */
            $wrapper = $this->wrapperFactory->create();
            $wrapper->setData(array_merge(array('event' => $event), $data));

            \Magento\Profiler::start('OBSERVER:' . $observerConfig['name']);
            $this->_invoker->dispatch($observerConfig, $wrapper);
            \Magento\Profiler::stop('OBSERVER:' .  $observerConfig['name']);
        }
        \Magento\Profiler::stop('EVENT:' . $eventName);
    }
}
