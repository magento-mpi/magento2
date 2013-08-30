<?php
/**
 * Event configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Event_Config implements Magento_Core_Model_Event_ConfigInterface
{
    /**
     * Modules configuration model
     *
     * @var Magento_Core_Model_Event_Config_Data
     */
    protected $_dataContainer;

    /**
     * @param Magento_Core_Model_Event_Config_Data $dataContainer
     */
    public function __construct(Magento_Core_Model_Event_Config_Data $dataContainer)
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
