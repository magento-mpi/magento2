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

class Enterprise_Permissions_Model_Validator
{
    public function systemConfigEdit($observer)
    {
        if( !Mage::helper('permissions')->isSuperAdmin() ) {
            $website = $observer->getEvent()->getControllerAction()->getRequest()->getParam('website');
            $store = $observer->getEvent()->getControllerAction()->getRequest()->getParam('store');
            if( !Mage::helper('permissions')->hasScopeAccess($website, $store) ) {
                if( $url = Mage::helper('permissions')->getConfigRedirectUrl() ) {
                    $observer->getEvent()->getControllerAction()->getResponse()->setRedirect($url);
                } else {
                    $this->_raiseDenied($observer);
                }
            }
        }

        return $this;
    }

    public function systemConfigSave($observer)
    {
        if( Mage::helper('permissions')->isSuperAdmin() ) {
            return $this;
        }

        $request = $observer->getEvent()->getControllerAction()->getRequest();

        if( $request->getParam('store') && !Mage::helper('permissions')->hasScopeAccess(null, $request->getParam('store')) ) {
            $this->_raiseDenied($observer);
            return $this;
        }

        if( $request->getParam('website') && !Mage::helper('permissions')->hasScopeAccess($request->getParam('website'), null) ) {
            $this->_raiseDenied($observer);
            return $this;
        }

        return $this;
    }

    public function catalogProductEdit($observer)
    {
        $this->_validateScope($observer, 'adminhtml/catalog_product/edit/');
        return $this;
    }

    public function catalogProductList($observer)
    {
        $this->_validateScope($observer, 'adminhtml/catalog_product/index/');
        return $this;
    }

    public function catalogProductAttributesEdit($observer)
    {
        $this->_validateScope($observer, 'adminhtml/catalog_product_action_attribute/edit/');
        return $this;
    }

    public function catalogProductSave($observer)
    {
        $this->_validateScope($observer, 'adminhtml/catalog_product/edit/');
        return $this;
    }

    public function dashboardView($observer)
    {
        $this->_validateScope($observer);
        return $this;
    }

    public function catalogCategoryEdit($observer)
    {
        $store = (int) $observer->getEvent()->getControllerAction()->getRequest()->getParam('store');
        if( $store <= 0 ) {
            $allowedStores = Mage::helper('permissions')->getAllowedStoreViews();
            $store = Mage::getModel('core/store')->load(array_shift($allowedStores));
        }
        $parent = Mage::app()->getStore($store)->getRootCategoryId();
        $this->_validateScope($observer, false, array('id' => $parent, '_current' => true));
        return $this;
    }

    protected function _validateScope($observer, $redirectUri=false, $urlParams=false)
    {
        if( !Mage::helper('permissions')->isSuperAdmin() ) {
            $store = $observer->getEvent()->getControllerAction()->getRequest()->getParam('store');

            if( !Mage::helper('permissions')->hasScopeAccess(null, $store) ) {
                $allowedStores = Mage::helper('permissions')->getAllowedStoreViews();

                if( sizeof($allowedStores) > 0 ) {
                    $store = Mage::getModel('core/store')->load(array_shift($allowedStores));
                    $params = array(
                        'store' => $store->getId(),
                        'id' => $observer->getEvent()->getControllerAction()->getRequest()->getParam('id')
                    );

                    if( $urlParams && is_array($urlParams) ) {
                        $params = array_merge($params, $urlParams);
                    }

                    $url = Mage::getUrl( $redirectUri ? $redirectUri : '*/*/*', $params);
                } else {
                    $url = false;
                }
                if( $url ) {
                    $observer->getEvent()->getControllerAction()->getResponse()->setRedirect($url);
                } else {
                    $this->_raiseDenied($observer);
                }
            }
        }
        return $this;
    }

    protected function _raiseDenied($observer)
    {
        $observer->getEvent()->getControllerAction()->getResponse()->setRedirect(Mage::getUrl('adminhtml/index/denied'));
    }
}