<?php
/**
 * Event configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Event;

class Config implements \Magento\Core\Model\Event\ConfigInterface
{
    /**
     * Modules configuration model
     *
     * @var \Magento\Core\Model\Event\Config\Data
     */
    protected $_dataContainer;

    /**
     * @param \Magento\Core\Model\Event\Config\Data $dataContainer
     */
    public function __construct(\Magento\Core\Model\Event\Config\Data $dataContainer)
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
