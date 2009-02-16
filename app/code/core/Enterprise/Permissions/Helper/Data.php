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
    public function isSuperAdmin()
    {
        return (bool) Mage::getSingleton('admin/session')->getUser()->getRole()->getIsAllPermissions();
    }

    public function getAllowedWebsites()
    {
        return (array) Mage::getSingleton('admin/session')->getUser()->getRole()->getWebsiteIds();
    }

    public function getAllowedStoreGroups()
    {
        return (array) Mage::getSingleton('admin/session')->getUser()->getRole()->getStoreGroupIds();
    }

    public function getAllowedStoreViews()
    {
        return ( array ) Mage::getSingleton('admin/session')->getUser()->getRole()->getStoreIds();
    }

    public function hasConfigAccess($website=null, $store=null)
    {
        $allowed = false;

        if( !is_null($store) && $store ){
            $model = Mage::getModel('core/store')->load($store);
            if( $model->getStoreId() && in_array($model->getStoreId(), $this->getAllowedStoreViews()) ) {
                $allowed = true;
            }
        }

        if( !is_null($website) && $website ){
            $model = Mage::getModel('core/website')->load($website);
            if( $model->getWebsiteId() && in_array($model->getWebsiteId(), $this->getAllowedWebsites()) ) {
                $allowed = true;
            }
        }

        return $allowed;
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