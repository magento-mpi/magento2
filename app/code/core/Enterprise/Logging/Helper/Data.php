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

class Enterprise_Logging_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_config;
    private $_labels;

    public function loadConfig()
    {
        $actions = Mage::app()->loadCache('enterprise_logging_actions');
        $labels  = Mage::app()->loadCache('enterprise_logging_labels');

        if (true || !($actions && $labels)) {
            $config = new Varien_Simplexml_Config;
            $config->loadString('<?xml version="1.0"?><logging></logging>');
            Mage::getConfig()->loadModulesConfiguration('logging.xml', $config);

            $this->_config = $config->getNode('actions')->asArray();
            Mage::app()->saveCache(serialize($this->_config), 'enterprise_logging_actions');

            $this->_labels = $config->getNode('labels')->asArray();
            $this->_labels = $this->_labels['list'];
            Mage::app()->saveCache(serialize($this->_labels), 'enterprise_logging_labels');
        }
        else {
            $this->_config = unserialize($actions);
            $this->_labels = unserialize($labels);
        }
    }

    /**
     * Filter if we need to log this action
     *
     * @param string action - fullActionName with removed 'adminhtml_' prefix
     */
    public function isActive($action)
    {
        if (!isset($this->_config)) {
            $this->loadConfig();
        }
        $current = isset($this->_config[$action]) ? $this->_config[$action] : false;
        if (!$current) {
            return false;
        }

        $code = $current['event'];
        /**
         * Note that /default/logging/enabled/products - is an indicator if the products should be logged
         * but /enterprise/logging/event/products - is a node where event info stored.
         */
        $node = Mage::getConfig()->getNode('default/admin/enterprise_logging/' . $code);
        return ( (string)$node == '1' ? true : false);
    }

    /**
     * Return, previously stored in cache config
     */
    public function getConfig($action)
    {
        if (!isset($this->_config)) {
            $this->loadConfig();
        }
        if (!isset($this->_config[$action])) {
            return null;
        }
        $this->_config[$action]['base_action'] = $action;
        return $this->_config[$action];
    }

    /**
     * Get all labels
     */
    public function getLabels()
    {
        if (!isset($this->_labels)) {
            $this->loadConfig();
        }
        asort($this->_labels);
        return $this->_labels;
    }

    /**
     * Get label for current event_code
     */
    public function getLabel($code)
    {
        if (!isset($this->_labels)) {
            $this->loadConfig();
        }
        $labelsconfig = $this->getLabels();
        return isset($labelsconfig[$code]) ? $labelsconfig[$code] : "";
    }
}
