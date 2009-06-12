<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Logging helper
 *
 */
class Enterprise_Logging_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * logging.xml merged config
     *
     * @var Varien_Simplexml_Config
     */
    protected $_config;

    /**
     * Translated and sorted labels
     *
     * @var array
     */
    private $_labels = array();

    /**
     * Load config from cache or merged from logging.xml files
     *
     */
    public function __construct()
    {
        if (!$this->_config) {
            $configXml = Mage::app()->loadCache('enterprise_logging_config');
            if ($configXml) {
                $this->_config = new Varien_Simplexml_Config($configXml);
            } else {
                $config = new Varien_Simplexml_Config;
                $config->loadString('<?xml version="1.0"?><logging></logging>');
                Mage::getConfig()->loadModulesConfiguration('logging.xml', $config);
                $this->_config = $config;
                Mage::app()->saveCache($config->getXmlString(), 'enterprise_logging_config',
                    array(Mage_Core_Model_Config::CACHE_TAG));
            }
        }
    }

    /**
     * Check whether specified full action name or event group should be logged
     *
     * @param string $reference
     * @param bool $isGroup
     */
    public function isActive($reference, $isGroup = false)
    {
        if ($isGroup) {
            return Mage::getStoreConfig("admin/enterprise_logging/{$reference}");
        }
        foreach ($this->_getNodesByFullActionName($reference) as $action) {
            /* @var $action Varien_Simplexml_Element */
            if (Mage::getStoreConfigFlag("admin/enterprise_logging/{$action->getParent()->getParent()->getName()}")) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get configuration for specified full action name
     *
     * @param string $fullActionName
     * @return Varien_Simplexml_Element|false
     */
    public function getConfig($fullActionName)
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
            foreach ($this->_config->getXpath('/logging/*/label') as $labelNode) {
                $helperName = $labelNode->getParent()->getAttribute('module');
                if (!$helperName) {
                    $helperName = 'enterprise_logging';
                }
                $this->_labels[$labelNode->getParent()->getName()] = Mage::helper($helperName)->__((string)$labelNode);
            }
            asort($this->_labels);
        }
        return $this->_labels;
    }

    /**
     * Get label for specified event group code
     *
     * @return string
     */
    public function getLabel($groupCode)
    {
        $this->getLabels();
        if (isset($this->_labels[$groupCode])) {
            return $this->_labels[$groupCode];
        }
        return '';
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
        $actionNodes = $this->_config->getXpath("/logging/*/events/{$fullActionName}[1]");
        if ($actionNodes) {
            return $actionNodes;
        }
        return array();
    }
}
