<?php
/**
 * Event configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Event_Config
{
    /**
     * Modules configuration model
     *
     * @var Magento_Core_Model_Config_Modules
     */
    protected $_config;

    /**
     * Configuration for events by area
     *
     * @var array
     */
    protected $_eventAreas = array();

    /**
     * @param Magento_Core_Model_Config_Modules $config
     */
    public function __construct(Magento_Core_Model_Config_Modules $config)
    {
        $this->_config = $config;
    }

    /**
     * Get area events configuration
     *
     * @param   string $area event area
     * @return  Magento_Core_Model_Config_Element
     */
    protected function _getAreaEvent($area)
    {
        if (!isset($this->_eventAreas[$area])) {
            $this->_eventAreas[$area] = $this->_config->getNode($area)->events;
        }
        return $this->_eventAreas[$area];
    }

    /**
     * Populate event manager with area event observers
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param $area
     */
    public function populate(Magento_Core_Model_Event_Manager $eventManager, $area)
    {
        $areaConfig = $this->_getAreaEvent($area);
        if (!$areaConfig) {
            return;
        }

        foreach($areaConfig->children() as $eventName => $eventConfig) {
            $observers = array();
            $eventObservers = $eventConfig->observers->children();
            if (!$eventObservers) {
                $eventManager->addObservers($area, $eventName, $observers);
                continue;
            }

            /** @var $obsConfig Magento_Core_Model_Config_Element */
            foreach ($eventObservers as $obsName => $obsConfig) {
                $observers[$obsName] = array(
                    'type'   => (string)$obsConfig->type,
                    'model'  => $obsConfig->class ? (string)$obsConfig->class : $obsConfig->getClassName(),
                    'method' => (string)$obsConfig->method,
                    'config' => $obsConfig->asArray(),
                );
            }
            $eventManager->addObservers($area, $eventName, $observers);
        }
    }
}
