<?php

class Varien_Event_Observer_Regex extends Varien_Event_Observer
{
    public function isValidFor(Varien_Event $event)
    {
        return preg_match($this->getEventRegex(), $event->getName());
    }
}