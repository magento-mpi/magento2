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

class Config implements \Magento\Event\ConfigInterface
{
    /**
     * Modules configuration model
     *
     * @var \Magento\Event\Config\Data
     */
    protected $_dataContainer;

    /**
     * @param \Magento\Event\Config\Data $dataContainer
     */
    public function __construct(Config\Data $dataContainer)
    {
        $this->_dataContainer = $dataContainer;
    }

    /**
     * Get observers by event name
     *
     * @param $eventName
     * @return array
     */
    public function getObservers($eventName)
    {
        return $this->_dataContainer->get($eventName, array());
    }
}
