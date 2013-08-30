<?php
/**
 * Event configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Event_Config implements Mage_Core_Model_Event_ConfigInterface
{
    /**
     * Modules configuration model
     *
     * @var Mage_Core_Model_Event_Config_Data
     */
    protected $_dataContainer;

    /**
     * @param Mage_Core_Model_Event_Config_Data $dataContainer
     */
    public function __construct(Mage_Core_Model_Event_Config_Data $dataContainer)
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
