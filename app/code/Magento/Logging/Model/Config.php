<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Logging configuration model
 *
 * Merges logging.xml files and provides access to nodes and labels
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
     * Load config from cache or merged from logging.xml files
     *
     * @param Magento_Core_Model_Config_Modules_Reader $configReader
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $configReader,
        Magento_Core_Model_Cache_Type_Config $configCacheType
    ) {
        $configXml = $configCacheType->load('magento_logging_config');
        if ($configXml) {
            $this->_xmlConfig = new Magento_Simplexml_Config($configXml);
        } else {
            $config = new Magento_Simplexml_Config;
            $config->loadString('<?xml version="1.0"?><logging></logging>');
            $configReader->loadModulesConfiguration('logging.xml', $config);
            $this->_xmlConfig = $config;
            $configCacheType->save($config->getXmlString(), 'magento_logging_config');
        }
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
                foreach ($this->getLabels() as $key => $label) {
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
     * Get configuration node for specified full action name
     *
     * @param string $fullActionName
     * @return Magento_Simplexml_Element|false
     */
    public function getNode($fullActionName)
    {
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
            foreach ($this->_xmlConfig->getXpath('/logging/*/label') as $labelNode) {
                $this->_labels[$labelNode->getParent()->getName()] = __((string)$labelNode);
            }
            asort($this->_labels);
        }
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
        $xpath = 'actions/' . $action . '/label';
        $actionLabelNode = $this->_xmlConfig->getNode($xpath);

        if (!$actionLabelNode) {
            return $action;
        }

        return __((string)$actionLabelNode);
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
        $actionNodes = $this->_xmlConfig->getXpath("/logging/*/actions/{$fullActionName}[1]");
        if ($actionNodes) {
            return $actionNodes;
        }
        return array();
    }
}
