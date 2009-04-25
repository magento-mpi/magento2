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
 * @package    Enterprise_AdminGws
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Controllers AdminGws validator
 */
class Enterprise_AdminGws_Model_Controllers
{
    /**
     * @var Enterprise_AdminGws_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var bool
     */
    protected $_isForwarded = false;

    /**
     * Initialize helper
     *
     */
    public function __construct()
    {
        $this->_helper  = Mage::helper('enterprise_admingws');
        $this->_request = Mage::app()->getRequest();
    }

    /**
     * Make sure the System Configuration pages are used in proper scopes
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateSystemConfig($controller)
    {
        // allow specific store view scope
        if ($storeCode = $this->_request->getParam('store')) {
            if ($store = Mage::app()->getStore($storeCode)) {
                if ($this->_helper->hasStoreAccess($store->getId())) {
                    return;
                }
            }
        }
        // allow specific website scope
        elseif ($websiteCode = $this->_request->getParam('website')) {
            if ($website = Mage::app()->getWebsite($websiteCode)) {
                if ($this->_helper->hasWebsiteAccess($website->getId(), true)) {
                    return;
                }
            }
        }

        // redirect to first allowed website or store scope
        if ($this->_helper->getWebsiteIds()) {
            return $this->_redirect($controller, Mage::getUrl('adminhtml/system_config/edit',
                array('website' => Mage::app()->getAnyStoreView()->getWebsite()->getCode()))
            );
        }
        $this->_redirect($controller, Mage::getUrl('adminhtml/system_config/edit',
            array('website' => Mage::app()->getAnyStoreView()->getWebsite()->getCode(), 'store' => Mage::app()->getAnyStoreView()->getCode()))
        );
    }

    /**
     * Validate misc catalog product requests
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogProduct($controller)
    {
        $this->validateNoWebsiteGeneric($controller, array('new', 'delete', 'duplicate'));
        if ($this->_isForwarded) {
            return;
        }

        // allow saving only in allowed store scope
        if ($storeId = $this->_request->getParam('store')) {
            if ($store = Mage::app()->getStore($storeId)) {
                if ($this->_helper->hasStoreAccess($store->getId())) {
                    return;
                }
            }
            $this->_forward();
        }
    }

    /**
     * Validate catalog product edit page
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogProductEdit($controller)
    {
        // avoid viewing disallowed product
        $product = Mage::getModel('catalog/product')->load($this->_request->getParam('id'));
        $productWebsiteIds = $product->getResource()->getWebsiteIds($product);
        if ((!$product->getId()) || (count(array_diff($productWebsiteIds, $this->_helper->getRelevantWebsiteIds())) === count($productWebsiteIds))) {
            return $this->_redirect($controller, '*/*/');
        }

        // redirect from disallowed scope
        if ($this->_isDisallowedStoreInRequest()) {
            return $this->_redirect($controller, array('*/*/*', 'id' => $product->getId()));
        }
    }

    /**
     * Avoid viewing disallowed customer
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCustomerEdit($controller)
    {
        $customer = Mage::getModel('customer/customer')->load($this->_request->getParam('id'));
        if ((!$customer->getId()) || !in_array($customer->getWebsiteId(), $this->_helper->getRelevantWebsiteIds())) {
            return $this->_forward();
        }
    }

    /**
     * Avoid viewing disallowed customer balance
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCustomerbalance()
    {
        if (!$id = $this->_request->getParam('id')) {
            return $this->_forward();
        }
        $customer = Mage::getModel('customer/customer')->load($id);
        if ((!$customer->getId()) || !in_array($customer->getWebsiteId(), $this->_helper->getRelevantWebsiteIds())) {
            return $this->_forward();
        }
    }

    /**
     * Disallow submitting gift cards without website-level permissions
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateGiftCardAccount($controller)
    {
        $this->validateNoWebsiteGeneric($controller, array('new', 'delete', 'generate'));
    }

    /**
     * Disallow saving categories in disallowed scopes
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogCategories($controller)
    {
        // creating
        // saving
        // moving
        // deleting
    }

    /**
     * Disallow viewing categories in disallowed scopes
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogCategoryView($controller)
    {
        // total mess - cannot do anything in current implementation
    }

    /**
     * Disallow submitting catalog event in wrong scope
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogEvents($controller)
    {
        // instead of generic (we are capped by allowed store groups root categories)
        // check whether attempting to create event for wrong category
        if ('new' === $this->_request->getActionName()) {
            $category = Mage::getModel('catalog/category')->load($this->_request->getParam('category_id'));
            if (!$this->_isCategoryAllowed($category)) {
                return $this->_forward();
            }
        }
        // or to delete for wrong category
        elseif ('delete' === $this->_request->getActionName()) {
            $catalogEvent = Mage::getModel('enterprise_catalogevent/event')->load($this->_request->getParam('id'));
            $category     = Mage::getModel('catalog/category')->load($catalogEvent->getCategoryId());
            if (!$this->_isCategoryAllowed($category)) {
                return $this->_forward();
            }
        }

        // disallow actions in wrong store scope
        if ($this->_isDisallowedStoreInRequest()) {
            return $this->_forward();
        }
    }

    /**
     * Disallow viewing wrong catalog events or viewing them in disallowed scope
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogEventEdit($controller)
    {
        // avoid viewing disallowed events
        $catalogEvent = Mage::getModel('enterprise_catalogevent/event')->load($this->_request->getParam('id'));
        $category     = Mage::getModel('catalog/category')->load($catalogEvent->getCategoryId());
        if (!$this->_isCategoryAllowed($category)) {
            return $this->_forward();
        }

        // redirect from disallowed store scope
        if ($this->_isDisallowedStoreInRequest()) {
            return $this->_redirect($controller, array('*/*/*', 'store' => Mage::app()->getAnyStoreView()->getId(), 'id' => $catalogEvent->getId()));
        }
    }

    /**
     * Disallow any creation order activity, if there is no website-level access
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateSalesOrderCreation($controller)
    {
        if (!$this->_helper->getWebsiteIds()) {
            return $this->_forward();
        }

        // check whether there is disallowed website in request?
    }

// TODO allow viewing sales information only from allowed websites

    /**
     * Don't allow to create or save entity, if there is no website permissions
     *
     * @param Mage_Adminhtml_Controller_Action $controller (first param is reserved, don't remove it)
     * @param string|array $denyActions
     * @param string $saveAction
     * @param string $idFieldName
     * @return null
     */
    public function validateNoWebsiteGeneric($controller = null, $denyActions = array('new', 'delete'), $saveAction = 'save', $idFieldName = 'id')
    {
        if (!is_array($denyActions)) {
            $denyActions = array($denyActions);
        }
        if ((!$this->_helper->getWebsiteIds()) && (in_array($this->_request->getActionName(), $denyActions)
                || ($saveAction === $this->_request->getActionName() && 0 == $this->_request->getParam($idFieldName)))) {
            return $this->_forward();
        }
    }

    /**
     * Redirect to a specific page
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    protected function _redirect($controller, $url = null)
    {
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        if (null === $url) {
            $url = Mage::getUrl('*/*/denied');
        }
        elseif (is_array($url)) {
            $url = Mage::getUrl(array_shift($url), $url);
        }
        elseif (false === strpos($url, 'http', 0)) {
            $url = Mage::getUrl($url);
        }
        Mage::app()->getResponse()->setRedirect($url);
    }

    /**
     * Forward current request
     *
     * @param string $action
     * @param string $module
     * @param string $controller
     */
    protected function _forward($action = 'denied', $module = null, $controller = null)
    {
        // avoid cycling
        if ($this->_request->getActionName() === $action
            && (null === $module || $this->_request->getModuleName() === $module)
            && (null === $controller || $this->_request->getControllerName() === $controller)) {
            return;
        }

        if ($module) {
            $this->_request->setModuleName($module);
        }
        if ($controller) {
            $this->_request->setControllerName($controller);
        }
        $this->_request->setActionName($action)->setDispatched(false);
        $this->_isForwarded = true;
    }

    /**
     * Check whether a disallowed store is in request
     *
     * @param string $idFieldName
     * @return bool
     */
    protected function _isDisallowedStoreInRequest($idFieldName = 'store')
    {
        $store = Mage::app()->getStore($this->_request->getParam($idFieldName), 0);
        return ($store->isAdmin() ? false : !$this->_helper->hasStoreAccess($store->getId()));
    }

    /**
     * Check whether specified category is allowed
     *
     * @param Mage_Catalog_Model_Category $category
     * @return bool
     */
    protected function _isCategoryAllowed($category)
    {
        if (!$category->getId()) {
            return false;
        }
        $categoryPath = $category->getPath();
        foreach ($this->_helper->getAllowedRootCategories() as $rootPath) {
            if ($categoryPath === $rootPath || 0 === strpos($categoryPath, "{$rootPath}/")) {
                return true;
            }
        }
        return false;
    }
}
