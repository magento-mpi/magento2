<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Event
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Event;

use \Magento\Event;
use \Magento\Object;

class Observer extends Object
{
    /**
     * Checks the observer's event_regex against event's name
     *
     * @param Event $event
     * @return boolean
     */
    public function isValidFor(Event $event)
    {
        return $this->getEventName() === $event->getName();
    }

    /**
     * Dispatches an event to observer's callback
     *
     * @param Event $event
     * @return $this
     */
    public function dispatch(Event $event)
    {
        if (!$this->isValidFor($event)) {
            return $this;
        }

        $callback = $this->getCallback();
        $this->setEvent($event);

        $_profilerKey = 'OBSERVER: '.(is_object($callback[0]) ? get_class($callback[0]) : (string)$callback[0]).' -> '.$callback[1];
        \Magento\Profiler::start($_profilerKey);
        call_user_func($callback, $this);
        \Magento\Profiler::stop($_profilerKey);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * @param string $data
     * @return Object
     */
    public function setName($data)
    {
        return $this->setData('name', $data);
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->getData('event_name');
    }

    /**
     * @param string $data
     * @return Object
     */
    public function setEventName($data)
    {
        return $this->setData('event_name', $data);
    }

    /**
     * @return string
     */
    public function getCallback()
    {
        return $this->getData('callback');
    }

    /**
     * @param string $data
     * @return Object
     */
    public function setCallback($data)
    {
        return $this->setData('callback', $data);
    }

    /**
     * Get observer event object
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->getData('event');
    }

    /**
     * @param mixed $data
     * @return Object
     */
    public function setEvent($data)
    {
        return $this->setData('event', $data);
    }
}
