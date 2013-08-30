<?php
/**
 * Event configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_Event_ConfigInterface
{
    /**
     * Get observers by event name
     *
     * @param $eventName
     * @return array
     */
    public function getObservers($eventName);
}
