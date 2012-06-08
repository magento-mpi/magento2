<?php
class Mage_Core_Model_Event_Manager
{
    public function dispatch($eventName, $params)
    {
        return Mage::dispatchEvent($eventName, $params);
    }
}
