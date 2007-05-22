<?php

/**
 * Event observer object
 * 
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Varien
 * @subpackage  Event
 * @author      Moshe Gurvich <moshe@varien.com>
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
        
        if (isset($callback['object']) && isset($callback['method']) && is_object($callback['object'])) {
            $callback['object']->$callback['method']($this);
        }
        else {
            call_user_func($callback, $this);
        }
        
        return $this;
    }
}