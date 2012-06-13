<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Event manager. Used to dispatch global events
 */
class Mage_Core_Model_Event_Manager
{
    /**
     * Dispatch global event with provided params
     *
     * @param string $eventName
     * @param array $params
     * @return Mage_Core_Model_App
     */
    public function dispatch($eventName, $params)
    {
        return Mage::dispatchEvent($eventName, $params);
    }
}
