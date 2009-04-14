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
 * @package    Enterprise_Permissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Permissions_Model_System_Store extends Mage_Adminhtml_Model_System_Store
{
    protected function _cleanupCollection()
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $this;
        }

        if( is_array($this->_storeCollection) ) {
            $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();
            foreach ($this->_storeCollection as $storeId => $store) {
                if( !in_array($storeId, $allowedStores) ) {
                    unset($this->_storeCollection[$storeId]);
                }
            }
        }

        if( is_array($this->_groupCollection) ) {
            $allowedGroups = Mage::helper('enterprise_permissions')->getAllowedStoreGroups();
            foreach ($this->_groupCollection as $groupId => $group) {
                if( !in_array($groupId, $allowedGroups) ) {
                    unset($this->_groupCollection[$groupId]);
                }
            }
        }

        if( is_array($this->_websiteCollection) ) {
            $relevantWebsites = Mage::helper('enterprise_permissions')->getRelevantWebsites();
            foreach ($this->_websiteCollection as $websiteId => $website) {
                if( !in_array($websiteId, $relevantWebsites) ) {
                    unset($this->_websiteCollection[$websiteId]);
                }
            }
        }

        return $this;
    }

    protected function _forceDisableWebsitesAll()
    {
        return true;
    }

    protected function _getDefaultStoreOptions($empty=false, $all=false)
    {
        return array();
    }
}
