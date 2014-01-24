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
     * @var InvokerInterface
     */
    protected $_invoker;

    /**
     * Event config
     *
     * @var ConfigInterface
     */
    protected $_eventConfig;

    /**
     * @param InvokerInterface $invoker
     * @param ConfigInterface $eventConfig
     */
    public function __construct(
        InvokerInterface $invoker,
        ConfigInterface $eventConfig
    ) {
        $this->_invoker = $invoker;
        $this->_eventConfig = $eventConfig;
    }

    /**
     * Dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiple observers matching event name pattern
     *
     * @param string $eventName
     * @param array $data
     * @return void
     */
    public function dispatch($eventName, array $data = array())
    {
        \Magento\Profiler::start('EVENT:' . $eventName, array('group' => 'EVENT', 'name' => $eventName));
        foreach ($this->_eventConfig->getObservers($eventName) as $observerConfig) {
            $event = new \Magento\Event($data);
            $event->setName($eventName);

            $wrapper = new Observer();
            $wrapper->setData(array_merge(array('event' => $event), $data));

            \Magento\Profiler::start('OBSERVER:' . $observerConfig['name']);
            $this->_invoker->dispatch($observerConfig, $wrapper);
            \Magento\Profiler::stop('OBSERVER:' .  $observerConfig['name']);
        }
        \Magento\Profiler::stop('EVENT:' . $eventName);
    }
}
