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

class Magento_Logging_Model_Config
{
    /**
     * logging.xml merged config
     *
     * @var Magento_Simplexml_Config
     */
    protected $_xmlConfig;

    /**
     * Translated and sorted labels
     *
     * @var array
     */
    protected $_labels = array();

    protected $_systemConfigValues = null;

    /**
     * @var Magento_Core_Model_Fieldset_Config_Data
     */
    protected $_dataStorage;

    /**
     * @param Magento_Logging_Model_Config_Data $dataStorage
     */
    public function __construct(Magento_Logging_Model_Config_Data $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
        $this->_xmlConfig = $this->_dataStorage->get('logging');
    }

    /**
     * Current system config values getter
     *
     * @return array
     */
    public function getSystemConfigValues()
    {
        if (null === $this->_systemConfigValues) {
            $this->_systemConfigValues = Mage::getStoreConfig('admin/magento_logging/actions');
            if (null === $this->_systemConfigValues) {
                $this->_systemConfigValues = array();
                foreach (array_keys($this->getLabels()) as $key) {
                    $this->_systemConfigValues[$key] = 1;
                }
            }
            else {
                $this->_systemConfigValues = unserialize($this->_systemConfigValues);
            }
        }
        return $this->_systemConfigValues;
    }

    /**
     * Check whether specified full action name or event group should be logged
     *
     * @param string $reference
     * @param bool $isGroup
     * @return bool
     */
    public function isActive($reference, $isGroup = false)
    {
        if (!$isGroup) {
            foreach ($this->_getNodesByFullActionName($reference) as $action) {
                $reference = $action->getParent()->getParent()->getName();
                $isGroup   = true;
                break;
            }
        }

        if ($isGroup) {
            $this->getSystemConfigValues();
            return isset($this->_systemConfigValues[$reference]);
        }

        return false;
    }

    /**
     * Get event with specified full action name
     *
     * @param string $fullActionName
     * @return array|false
     */
    public function getEventByFullActionName($fullActionName)
    {
        /** @todo to be implemented */
    }

    /**
     * Get configuration node for specified full action name
     *
     * @param string $fullActionName
     * @return Magento_Simplexml_Element|false
     */
    public function getNode($fullActionName)
    {
        if (!$fullActionName) {
            return array();
        }
        foreach ($this->_getNodesByFullActionName($fullActionName) as $actionConfig) {
            return $actionConfig;
        }
        return false;
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
        var_dump($this->_labels); die();
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
        $actionLabel = $this->_xmlConfig['actions'][$action];
        if (!isset($actionLabel['label'])) {
            return $action;
        }

        return __($actionLabel['label']);
    }

    /**
     * Lookup configuration nodes by full action name
     *
     * @param string $fullActionName
     * @return array
     */
    protected function _getNodesByFullActionName($fullActionName)
    {
        if (!$fullActionName) {
            return array();
        }
        //$actionNodes = $this->_xmlConfig["logging/*/actions/{$fullActionName}[1]"];
        var_dump($fullActionName);
        foreach ($this->_xmlConfig as $configData) {
            if (isset($configData['actions']) && isset ($configData['actions'][$fullActionName])) {
                $actionNodes = $configData['actions'][$fullActionName];
            }
        }
        if ($actionNodes) {
            return $actionNodes;
        }
        return array();
    }
}
