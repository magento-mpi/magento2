<?php
/**
 * Event configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Event;

interface ConfigInterface
{
    /**
     * Get observers by event name
     *
     * @param string $eventName
     * @return array
     */
    public function getObservers($eventName);
}
