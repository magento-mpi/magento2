<?php
/**
 * Event configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Event;

interface ConfigInterface
{
    /**
     * Get observers by event name
     *
     * @param $eventName
     * @return array
     */
    public function getObservers($eventName);
}
