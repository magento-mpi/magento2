<?php

/**
 * Event regex observer object
 * 
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Varien
 * @subpackage  Event
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Varien_Event_Observer_Regex extends Varien_Event_Observer
{
    /**
     * Checkes the observer's event_regex against event's name
     *
     * @param Varien_Event $event
     * @return boolean
     */
    public function isValidFor(Varien_Event $event)
    {
        return preg_match($this->getEventRegex(), $event->getName());
    }
}