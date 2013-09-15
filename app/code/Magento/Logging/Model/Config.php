<?php
/**
 * Logging configuration model
 *
 * Provides access to nodes and labels
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Logging\Model;

class Config
{
    /**
     * logging.xml merged config
     *
     * @var array
     */
    protected $_xmlConfig;

    /**
     * Translated and sorted labels
     *
     * @var array
     */
    protected $_labels = array();

    /**
     * Configuration for event groups from System Configuration
     *
     * @var array
     */
    protected $_systemConfigValues = null;

    /**
     * @var Magento_Core_Model_Store
     */
    protected $_store;

    /**
     * @param Magento_Logging_Model_Config_Data $dataStorage
     */
    public function __construct(\Magento\Logging\Model\Config\Data $dataStorage,
        \Magento\Core\Model\StoreManager $storeManager
    ) {
        $this->_xmlConfig = $dataStorage->get('logging');
        $this->_store = $storeManager->getStore();
    }

    /**
     * Current system config values getter
     *
     * @return array
     */
    public function getSystemConfigValues()
    {
        if (null === $this->_systemConfigValues) {
            $this->_initSystemConfigValues();
        }
        return $this->_systemConfigValues;
    }

    /**
     * Check if there is a value identified by key in System Config
     *
     * @param string $key
     * @return bool
     */
    public function hasSystemConfigValue($key)
    {
        if (null === $this->_systemConfigValues) {
            $this->_initSystemConfigValues();
        }
        return isset($this->_systemConfigValues[$key]);
    }

    /**
     * Check if event group is enabled for logging
     *
     * @param string $groupName
     * @return bool
     */
    public function isEventGroupLogged($groupName)
    {
        return $this->hasSystemConfigValue($groupName);
    }

    /**
     * Get all labels translated and sorted ASC
     *
     * @return array
     */
    public function getLabels()
    {
        if (!$this->_labels) {
            foreach ($this->_xmlConfig as $logName => $logConfig) {
                if (isset($logConfig['label'])) {
                    $this->_labels[$logName] = __($logConfig['label']);
                }
            }
            asort($this->_labels);
        };
        return $this->_labels;
    }

    /**
     * Get logging action translated label
     *
     * @param string $action
     * @return string
     */
    public function getActionLabel($action)
    {
        if (isset($this->_xmlConfig['actions'])
            && array_key_exists($action, $this->_xmlConfig['actions'])
            && isset($this->_xmlConfig['actions'][$action]['label']))
        {
            return __($this->_xmlConfig['actions'][$action]['label']);
        }

        return $action;
    }

    /**
     * Get configuration node for specified full action name
     *
     * @param string $controllerAction
     * @return array|false
     */
    public function getEventByFullActionName($controllerAction)
    {
        foreach ($this->_xmlConfig as $configData) {
            if (isset($configData['actions']) && array_key_exists($controllerAction, $configData['actions'])) {
                return $configData['actions'][$controllerAction];
            }
        }
        return false;
    }

    /**
     * Retrieve configuration for group of events
     *
     * @param $groupName
     * @return bool
     */
    public function getEventGroupConfig($groupName)
    {
        if (!array_key_exists($groupName, $this->_xmlConfig)) {
            return false;
        }
        return $this->_xmlConfig[$groupName];
    }


    /**
     * Load values from System Configuration
     *
     * @return Magento_Logging_Model_Config
     */
    protected function _initSystemConfigValues()
    {
        $this->_systemConfigValues = $this->_store->getConfig('admin/magento_logging/actions');
        if (null === $this->_systemConfigValues) {
            $this->_systemConfigValues = array();
            foreach (array_keys($this->getLabels()) as $key) {
                $this->_systemConfigValues[$key] = 1;
            }
        } else {
            $this->_systemConfigValues = unserialize($this->_systemConfigValues);
        }
        return $this;
    }
}
