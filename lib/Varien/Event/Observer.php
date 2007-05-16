<?php

class Varien_Event_Observer extends Varien_Object
{
    public function dispatch(Varien_Event $event)
    {
        $callback = $this->getCallback();
        call_user_func($callback, $event);
        return $this;
    }
}