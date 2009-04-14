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

class Enterprise_Permissions_Model_Validate_Action extends Enterprise_Permissions_Model_Validate_Abstract
{
    public function systemConfigEdit()
    {
        if( !Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            $website = $this->_getRequest()->getParam('website');
            $store = $this->_getRequest()->getParam('store');
            if( !Mage::helper('enterprise_permissions')->hasScopeAccess($website, $store) ) {
                if( $url = Mage::helper('enterprise_permissions')->getConfigRedirectUrl() ) {
                    $this->_getObserver()->getEvent()->getControllerAction()->getResponse()->setRedirect($url);
                } else {
                    $this->_raiseDenied();
                }
            }
        }

        return $this;
    }

    public function systemConfigSave()
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $this;
        }

        $request = $this->_getRequest();

        if( $request->getParam('store') && !Mage::helper('enterprise_permissions')->hasScopeAccess(null, $request->getParam('store')) ) {
            $this->_raiseDenied();
            return $this;
        }

        if( $request->getParam('website') && !Mage::helper('enterprise_permissions')->hasScopeAccess($request->getParam('website'), null) ) {
            $this->_raiseDenied();
            return $this;
        }

        return $this;
    }

    public function catalogProductEdit()
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $this;
        }

        if( !$this->_getObserver()->getControllerAction()->getRequest()->getParam('id') ) {
            $this->catalogProductNew();
        }
        $this->_validateScope('adminhtml/catalog_product/edit/');
        return $this;
    }

    public function catalogProductNew()
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $this;
        }

        if( !Mage::helper('enterprise_permissions')->hasAnyWebsiteScopeAccess() ) {
            $this->_redirect('*/catalog_product/index');
        }

        if( !$this->_getObserver()->getControllerAction()->getRequest()->getParam('store') ) {
            $this->_redirect('*/*/*');
        }
        return $this;
    }

    public function catalogProductList()
    {
        $this->_validateScope('adminhtml/catalog_product/index/');
        return $this;
    }

    public function catalogProductAttributesEdit()
    {
        $this->_validateScope('adminhtml/catalog_product_action_attribute/edit/');
        return $this;
    }

    public function catalogProductSave()
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $this;
        }

        $post = $this->_getObserver()->getControllerAction()->getRequest()->getPost();
        if( !$this->_getObserver()->getControllerAction()->getRequest()->getParam('id')
            && (array_key_exists('product', $post)
            && (!isset( $post['product']['website_ids'] )
            || sizeof(is_array($post['product']['website_ids']) == 0 ))) ) {

            $post['product']['website_ids'] = Mage::helper('enterprise_permissions')->getAllowedWebsites();
        } else {
            $productId = $this->_getObserver()->getControllerAction()->getRequest()->getParam('id');
            $product = Mage::getModel('catalog/product')->load($productId);
            $websiteIds = $product->getWebsiteIds();
            $notAllowedWebsites = array_diff($websiteIds, Mage::helper('enterprise_permissions')->getAllowedWebsites());
            if( isset($post['product']['website_ids']) && is_array($post['product']['website_ids']) && is_array($notAllowedWebsites) ) {
                $post['product']['website_ids'] = array_merge($notAllowedWebsites, $post['product']['website_ids']);
            } else {
                $post['product']['website_ids'] = $notAllowedWebsites;
            }
        }

        $this->_getObserver()->getControllerAction()->getRequest()->setPost($post);

        $this->_validateScope('adminhtml/catalog_product/edit/');
        return $this;
    }

    public function dashboardView()
    {
        $this->_validateScope();
        return $this;
    }

    public function catalogCategoryEdit()
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $this;
        }
        $store = (int) $this->_getRequest()->getParam('store');
        if( $store <= 0 ) {
            $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();
            $store = Mage::getModel('core/store')->load(array_shift($allowedStores));
        }
        $parent = Mage::app()->getStore($store)->getRootCategoryId();
        $this->_validateScope(false, array('id' => $parent, '_current' => true));
        return $this;
    }

    public function salesOrderList()
    {
        if( !Mage::helper('enterprise_permissions')->hasAnyStoreScopeAccess() ) {
            $this->_raiseDenied();
        }
        return $this;
    }

    public function urlRewriteEdit()
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $this;
        }
        $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();

        if ($this->_getRequest()->has('product') && !$this->_getRequest()->getParam('store')) {
            $this->_getRequest()->setParam('store', array_shift($allowedStores));
        }
        elseif ($this->_getRequest()->has('category') && !$this->_getRequest()->getParam('store') ) {
            $this->_getRequest()->setParam('store', array_shift($allowedStores));
        }
        return $this;
    }

    public function salesOrderCreate()
    {
        if( !Mage::helper('enterprise_permissions')->hasAnyWebsiteScopeAccess() ) {
            $this->_redirect('*/sales_order/index');
        }
    }
    
    public function promoEdit()
    {
        if( !Mage::helper('enterprise_permissions')->hasAnyWebsiteScopeAccess() ) {
            $this->_redirect('*/*/index');
        }
    }
}