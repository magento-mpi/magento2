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

class Enterprise_Permissions_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_isSuperAdmin;
    protected $_allowedWebsites;
    protected $_relevantWebsites;
    protected $_allowedStoreGroups;
    protected $_allowedStoreViews;

    public function isSuperAdmin()
    {
        if (null === $this->_isSuperAdmin) {
            $this->_isSuperAdmin = (bool)Mage::getSingleton('admin/session')->getUser()->getRole()->getIsAllPermissions();
        }
        return $this->_isSuperAdmin;
    }

    public function getAllowedWebsites()
    {
        if (null === $this->_allowedWebsites) {
            $this->_allowedWebsites = (array)Mage::getSingleton('admin/session')->getUser()->getRole()->getWebsiteIds();
        }
        return $this->_allowedWebsites;
    }

    public function getRelevantWebsites()
    {
        if (null === $this->_relevantWebsites) {
            $this->_relevantWebsites = (array)Mage::getSingleton('admin/session')->getUser()->getRole()->getRelevantWebsiteIds();
        }
        return $this->_relevantWebsites;
    }

    public function getAllowedStoreGroups()
    {
        if (null === $this->_allowedStoreGroups) {
            $this->_allowedStoreGroups = (array)Mage::getSingleton('admin/session')->getUser()->getRole()->getStoreGroupIds();
        }
        return $this->_allowedStoreGroups;
    }

    public function getAllowedStoreViews()
    {
        if (null === $this->_allowedStoreViews) {
            $this->_allowedStoreViews = array();
            foreach ((array)Mage::getSingleton('admin/session')->getUser()->getRole()->getStoreIds() as $storeId) {
                $this->_allowedStoreViews[$storeId] = $storeId;
            }
        }
        return $this->_allowedStoreViews;
    }

    public function hasScopeAccess($website=null, $store=null)
    {
        $allowed = false;

        if( !is_null($store) && $store ){
            $model = Mage::app()->getStore($store);
            if( $model->getStoreId() && in_array($model->getStoreId(), $this->getAllowedStoreViews()) ) {
                $allowed = true;
            }
        }

        if( !is_null($website) && $website ){
            $model = Mage::app()->getWebsite($website);
            if( $model->getWebsiteId() && in_array($model->getWebsiteId(), $this->getAllowedWebsites()) ) {
                $allowed = true;
            }
        }

        return $allowed;
    }

    public function hasAnyWebsiteScopeAccess()
    {
        if( sizeof($this->getAllowedWebsites()) > 0 ) {
            return true;
        }

        return false;
    }

    public function hasAnyStoreScopeAccess()
    {
        if( sizeof($this->getAllowedStoreViews()) > 0 ) {
            return true;
        }

        return false;
    }

    public function getConfigRedirectUrl()
    {
        $allowedWebsites = $this->getAllowedWebsites();
        $allowedStores = $this->getAllowedStoreViews();

        if( sizeof($allowedWebsites) > 0 ) {
            $website = Mage::getModel('core/website')->load(array_shift($allowedWebsites));
            return Mage::getUrl('adminhtml/system_config/edit/', array('website' => $website->getCode()));
        }

        if( sizeof($allowedStores) > 0 ) {
            $store = Mage::getModel('core/store')->load(array_shift($allowedStores));
            return Mage::getUrl('adminhtml/system_config/edit/', array('store' => $store->getCode()));
        }

        return false;
    }
}
