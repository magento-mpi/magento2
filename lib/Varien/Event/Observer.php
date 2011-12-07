<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Event
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Event observer object
 *
 * @category   Varien
 * @package    Varien_Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Event_Observer extends Varien_Object
{
    /**
     * Checkes the observer's event_regex against event's name
     *
     * @param Varien_Event $event
     * @return boolean
     */
    public function isValidFor(Varien_Event $event)
    {
        return $this->getEventName()===$event->getName();
    }

    /**
     * Dispatches an event to observer's callback
     *
     * @param Varien_Event $event
     * @return Varien_Event_Observer
     */
    public function dispatch(Varien_Event $event)
    {
        if (!$this->isValidFor($event)) {
            return $this;
        }

        $callback = $this->getCallback();
        $this->setEvent($event);

        $_profilerKey = 'OBSERVER: '.(is_object($callback[0]) ? get_class($callback[0]) : (string)$callback[0]).' -> '.$callback[1];
        Magento_Profiler::start($_profilerKey);
        call_user_func($callback, $this);
        Magento_Profiler::stop($_profilerKey);

        return $this;
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function setName($data)
    {
        return $this->setData('name', $data);
    }

    public function getEventName()
    {
        return $this->getData('event_name');
    }

    public function setEventName($data)
    {
        return $this->setData('event_name', $data);
    }

    public function getCallback()
    {
        return $this->getData('callback');
    }

    public function setCallback($data)
    {
        return $this->setData('callback', $data);
    }

    /**
     * Get observer event object
     *
     * @return Varien_Event
     */
    public function getEvent()
    {
        return $this->getData('event');
    }

    public function setEvent($data)
    {
        return $this->setData('event', $data);
    }
}