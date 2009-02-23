<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Permissions_Model_System_Store extends Mage_Adminhtml_Model_System_Store
{
    protected function _cleanupCollection()
    {
        if( Mage::helper('permissions')->isSuperAdmin() ) {
            return $this;
        }

        if( is_array($this->_storeCollection) ) {
            $allowedStores = Mage::helper('permissions')->getAllowedStoreViews();
            foreach ($this->_storeCollection as $storeId => $store) {
                if( !in_array($storeId, $allowedStores) ) {
                    unset($this->_storeCollection[$storeId]);
                }
            }
        }

        if( is_array($this->_groupCollection) ) {
            $allowedGroups = Mage::helper('permissions')->getAllowedStoreGroups();
            foreach ($this->_groupCollection as $groupId => $group) {
                if( !in_array($groupId, $allowedGroups) ) {
                    unset($this->_groupCollection[$groupId]);
                }
            }
        }

        if( is_array($this->_websiteCollection) ) {
            $allowedWebsites = Mage::helper('permissions')->getAllowedWebsites();
            foreach ($this->_websiteCollection as $websiteId => $website) {
                if( !in_array($websiteId, $allowedWebsites) ) {
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
