<?php
/**
 * Event configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Event;

use \Magento\Event\Config\Data;

class Config implements ConfigInterface
{
    /**
     * Modules configuration model
     *
     * @var Data
     */
    protected $_dataContainer;

    /**
     * @param Data $dataContainer
     */
    public function __construct(Config\Data $dataContainer)
    {
        $this->_dataContainer = $dataContainer;
    }

    /**
     * Get observers by event name
     *
     * @param string $eventName
     * @return null|array|mixed
     */
    public function getObservers($eventName)
    {
        return $this->_dataContainer->get($eventName, array());
    }
}
