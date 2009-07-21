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
    protected $_observer;

    public function setObserver($observer)
    {
        $this->_observer = $observer;
    }

    /**
     * @return Varien_Event_Observer
     */
    protected function _getObserver()
    {
        return $this->_observer;
    }

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
        if( !$this->_getObserver()->getControllerAction()->getRequest()->getParam('id') ) {
            $this->catalogProductNew();
        }
        $this->_validateScope('adminhtml/catalog_product/edit/');
        return $this;
    }

    public function catalogProductNew()
    {
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
    }

    public function filterCustomerGrid($collection, $request)
    {
        $collection->addAttributeToFilter('website_id', array('IN' => Mage::helper('enterprise_permissions')->getRelevantWebsites()));
    }

    public function filterCatalogProductGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }
        $collection->addStoreFilter($request->getParam('store'));
        return $collection;
    }

    public function filterCatalogProductTagGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }
        $collection->addStoreFilter($request->getParam('store'));
        return $collection;
    }

    public function filterCatalogProductReviewGrid($collection, $request, $filterValues)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !is_array($filterValues) || !isset($filterValues['visible_in']) ) {
        	$collection->setStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }

        return $collection;
    }

    public function filterReviewGrid($collection, $request, $filterValues)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !is_array($filterValues) || !isset($filterValues['visible_in']) ) {
        	$collection->setStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }

        return $collection;
    }

    public function filterSalesOrderGrid($collection, $request, $filterValues)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
            $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();
            $storeId = $request->getParam('store') ? $request->getParam('store') : array('IN' => $allowedStores);
            $collection->addAttributeToFilter('store_id', $storeId);
        }

        return $collection;
    }

    public function filterSalesInvoiceGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        $collection->addAttributeToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));

        return $collection;
    }

    public function filterSalesShipmentGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        $collection->addAttributeToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));

        return $collection;
    }

    public function filterSalesCreditmemoGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        $collection->addAttributeToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));

        return $collection;
    }

    public function filterUrlrewriteGrid($collection, $request, $filterValues)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
            $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();
            $storeId = $request->getParam('store') ? $request->getParam('store') : array('IN' => $allowedStores);
            $collection->addFieldToFilter('store_id', $storeId);
        }

        return $collection;
    }

    public function filterCatalogSearchGrid($collection, $request, $filterValues)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
            $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();
            $storeId = $request->getParam('store') ? $request->getParam('store') : array('IN' => $allowedStores);
            $collection->addFieldToFilter('store_id', $storeId);
        }

        return $collection;
    }

    public function filterNewsletterSubscriberGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }
        $collection->addFieldToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));
        return $collection;
    }

    public function filterReportCommonGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if (!$request->getParam('store') && !$request->getParam('website') && !$request->getParam('group') ) {
            $collection->setStoreIds(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }

        return $collection;
    }

    public function filterReportShopcartProductGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if (!$request->getParam('store') && !$request->getParam('website') && !$request->getParam('group') ) {
    	   $collection->addWebsiteFilter(Mage::helper('enterprise_permissions')->getAllowedWebsites());
        }

        return $collection;
    }

    public function filterReportShopcartAbandonedGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !$request->getParam('website') && !$request->getParam('store') && !$request->getParam('group') ) {
        	$collection->addFieldToFilter('website_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedWebsites()));
        }

        return $collection;
    }

    public function filterReportTagCustomerGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !$request->getParam('website') && !$request->getParam('store') && !$request->getParam('group') ) {
        	$collection->addFieldToFilter('website_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedWebsites()));
        }

        return $collection;
    }

    public function filterReportTagProductGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !$request->getParam('website') && !$request->getParam('store') && !$request->getParam('group') ) {
        	$collection->addWebsiteFilter(Mage::helper('enterprise_permissions')->getAllowedWebsites());
        }

        return $collection;
    }

    public function filterReportTagPopularGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !$request->getParam('website') && !$request->getParam('store') && !$request->getParam('group') ) {
        	$collection->addStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }

        return $collection;
    }

    public function filterReportSearchGrid($collection, $request, $filterValues)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
        	$collection->addFieldToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));
        }

        return $collection;
    }

    public function filterCmsCommonGrid($collection, $request, $filterValues)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
        	$collection->addStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }

        return $collection;
    }

    public function filterPollGrid($collection, $request, $filterValues)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !is_array($filterValues) || !isset($filterValues['visible_in']) ) {
        	$collection->addStoresFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }

        return $collection;
    }

    public function filterSystemDesignGrid($collection, $request, $filterValues)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
        	$collection->addStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }

        return $collection;
    }

    public function filterRatingGrid($collection, $request)
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return $collection;
        }

        $collection->setStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());

        return $collection;
    }

    protected function _validateScope($redirectUri=false, $urlParams=false)
    {
        if( !Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            $store = $this->_getRequest()->getParam('store');

            if( !Mage::helper('enterprise_permissions')->hasScopeAccess(null, $store) ) {
                $this->_redirect($redirectUri, $urlParams);
            }
        }
        return $this;
    }

    protected function _redirect($redirectUri=false, $urlParams=false)
    {
        $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();

        if( sizeof($allowedStores) > 0 ) {
            $store = Mage::getModel('core/store')->load(array_shift($allowedStores));
            $params = array(
                'store' => $store->getId(),
                'id' => $this->_getRequest()->getParam('id'),
                '_current' => true
            );

            if( $urlParams && is_array($urlParams) ) {
                $params = array_merge($params, $urlParams);
            }

            $url = Mage::getSingleton('adminhtml/url')->getUrl( $redirectUri ? $redirectUri : '*/*/*', $params);
//            $url = Mage::getUrl( $redirectUri ? $redirectUri : '*/*/*', $params);
        } else {
            $url = false;
        }
        if( $url ) {
            $this->_getObserver()->getEvent()->getControllerAction()->getResponse()->setRedirect($url);
        } else {
            $this->_raiseDenied();
        }
    }

    protected function _raiseDenied()
    {
        $this->_getObserver()
             ->getEvent()
             ->getControllerAction()
             ->getResponse()
             ->setRedirect(Mage::getUrl('adminhtml/index/denied'));
    }

    protected function _getRequest()
    {
        return $this->_getObserver()->getEvent()->getControllerAction()->getRequest();
    }
}
