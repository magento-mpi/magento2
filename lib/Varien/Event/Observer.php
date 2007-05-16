<?php

class Varien_Event_Observer extends Varien_Object
{
    public function isValidFor(Varien_Event $event)
    {
        return $this->getEventName()===$event->getName();
    }
    
    public function dispatch(Varien_Event $event)
    {
        if (!$this->isValidFor($event)) {
            return $this;
        }
        
        $callback = $this->getCallback();
        $this->setEvent($event);
        call_user_func($callback, $this);
        
        return $this;
    }
}