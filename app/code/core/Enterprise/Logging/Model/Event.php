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

class Enterprise_Logging_Model_Event extends Mage_Core_Model_Abstract
{
    /**
     * configuration
     */
    const   CONFIG_FILE = 'logging.xml';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_init('enterprise_logging/event');
    }

    /**
     * Filter if we need to log this action
     *
     * @param string action - fullActionName with removed 'adminhtml_' prefix
     */
    public function isActive($action)
    {
        if( !($conf = Mage::app()->loadCache('actions_to_log')) ) {
            $conf = $this->_getActionsConfigFromXml();
            $result = Mage::app()->saveCache(serialize($conf), 'actions_to_log');

            $labelslist = $this->_getLabelsConfigFromXml(); 
            Mage::app()->getCache()->save(serialize($labelslist['list']), 'actions_to_log_labels');
        } else
            $conf = unserialize($conf);

        $current = isset($conf[$action]) ? $conf[$action] : false;
        if (!$current)
            return false;

        $code = $current['event'];
        /**
         * Note that /default/logging/enabled/products - is an indicator if the products should be logged
         * but /enterprise/logging/event/products - is a node where event info stored.
         */
        $node = Mage::getConfig()->getNode('default/admin/logsenabled/' . $code);
        return ( (string)$node == '1' ? true : false);
    }

    /**
     * Return, previously stored in cache config
     */ 
    public function getConfig($action) {
        $fullconfig = unserialize(Mage::app()->loadCache('actions_to_log'));
        if (!$fullconfig) {
            $fullconfig = $this->_getActionsConfigFromXml();
        }

        if (!isset($fullconfig[$action]))
            return null;
        $fullconfig[$action]['base_action'] = $action;
        return $fullconfig[$action];
    }

    /**
     * Get all labels
     */
    public function getLabels() {
        $labelsconfig = unserialize(Mage::app()->loadCache('actions_to_log_labels'));
        if (!$labelsconfig) {
            $labelsconfig = $this->_getLabelsConfigFromXml();
            $labelsconfig = $labelsconfig['list'];
        }
        return $labelsconfig;
    }

    /**
     * Get label for current event_code
     */
    public function getLabel($code) {
        $labelsconfig = $this->getLabels();
        return isset($labelsconfig[$code]) ? $labelsconfig[$code] : "";
    }

    /**
     * Load actions from config
     */
    private function _getActionsConfigFromXml() {
        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();

        // check if local modules are disabled                                                                                                               
        $disableLocalModules = (string)$config->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));

        $configFile = $config->getModuleDir('etc', 'Enterprise_Logging').DS.'logging.xml';

        $logConfig = Mage::getModel('core/config_base');
        $logConfig->loadFile($configFile);

        $node = $logConfig->getNode('actions');
        $conf = $node->asArray();
        return $conf;
    }

    /**
     * Load labels from configuration file
     */
    private function _getLabelsConfigFromXml() {
        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();

        // check if local modules are disabled                                                                                                               
        $disableLocalModules = (string)$config->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));

        $configFile = $config->getModuleDir('etc', 'Enterprise_Logging').DS. self::CONFIG_FILE;

        $logConfig = Mage::getModel('core/config_base');
        $logConfig->loadFile($configFile);

        $node = $logConfig->getNode('labels');
        $conf = $node->asArray();
        return $conf;
    }

    /**
     * Filter user_id. Set username instead of user_id
     *
     * @param int $id
     * @return Enterprise_Logging_Model_Event
     */
    public function setUserId($id) {
        $user = Mage::getModel('admin/user')->load($id);
        $name = $user->getUsername();
        return $this->setUser($name);
    }


    /**
     * Filter for info
     *
     * Takes an array of paramaters required to build info message. Message is stored in config, in
     * path like: enterprise/logging/events/products/actions/success, in sprintf format.
     * Assumed, that parameters in info, follows in order they are required in pattern string
     *
     * @param array $info
     * @return Enterprise_Logging_Model_Event
     */
    public function setInfo($info)
    {
        $code = $info['event_code'];
        $this->setEventCode($code);
        $action = $info['event_action'];
        $this->setAction($action);
        if(isset($info['event_status']) && $info['event_status'] != $this->getSuccess()) {
            $this->setSuccess($info['event_status']);
        }

        $success = $this->getSuccess() ? 'success' : 'fail';
        $this->setStatus($success);
        return $this->setData('info', $info['event_message']);
    }
}
