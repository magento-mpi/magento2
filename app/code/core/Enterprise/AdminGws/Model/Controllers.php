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
class Enterprise_AdminGws_Model_Controllers extends Enterprise_AdminGws_Model_Observer_Abstract
{
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
        parent::__construct();
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
            try {
                if ($website = Mage::app()->getWebsite($websiteCode)) {
                    if ($this->_helper->hasWebsiteAccess($website->getId(), true)) {
                        return;
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                // redirect later from non-existing website
            }
        }

        // redirect to first allowed website or store scope
        if ($this->_helper->getWebsiteIds()) {
            return $this->_redirect($controller, Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_config/edit',
                array('website' => Mage::app()->getAnyStoreView()->getWebsite()->getCode()))
            );
        }
        $this->_redirect($controller, Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_config/edit',
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
        if ($customer->getId() && !in_array($customer->getWebsiteId(), $this->_helper->getRelevantWebsiteIds())) {
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
        $id = $controller->getRequest()->getParam('id', false);
        if (!$id && $controller->getRequest()->isPost()) {
            $info = $controller->getRequest()->getPost('info');
            if ($info && isset($info['giftcardaccount_id'])) {
                $id = $info['giftcardaccount_id'];
            }
        }

        if ($id) {
            $model = Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                ->load($id);

            if (!in_array($model->getWebsiteId(), $this->_helper->getWebsiteIds())) {
                $this->_forward();
                return;
            }
        }
    }

    /**
     * Disallow saving catalog rules in disallowed scopes
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validatePromoCatalog($controller)
    {
        $this->validateNoWebsiteGeneric($controller, array('new', 'delete', 'applyRules'));

        if (!$this->_isForwarded && ($id = $controller->getRequest()->getParam('id'))) {
            $rule = Mage::getModel('catalogrule/rule')->load($id);
            if (!$rule->getId() ||
                !$this->_helper->hasWebsiteAccess($rule->getWebsiteIds())) {
                $this->_forward();
            }
        }
    }

    /**
     * Disallow saving quote rules in disallowed scopes
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validatePromoQuote($controller)
    {
        $this->validateNoWebsiteGeneric($controller, array('new', 'delete', 'applyRules'));

        if (!$this->_isForwarded && ($id = $controller->getRequest()->getParam('id'))) {
            $rule = Mage::getModel('salesrule/rule')->load($id);
            if (!$rule->getId() ||
                !$this->_helper->hasWebsiteAccess($rule->getOrigData('website_ids'))) {
                $this->_forward();
            }
        }
    }

    /**
     * Disallow saving categories in disallowed scopes
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogCategories($controller)
    {
        if (!in_array($controller->getRequest()->getActionName(), array('edit', 'add')) ||
            $this->_helper->getIsAll()) {
            return;
        }

        $forward = false;

        if ($controller->getRequest()->getActionName() == 'add' ||
            !$controller->getRequest()->getParam('id')) {
            $forward = true;
        }

        if (!$forward) {
            $category = Mage::getModel('catalog/category')->load(
                $controller->getRequest()->getParam('id')
            );
            if (!$category->getId() ||
                !$this->_isCategoryAllowed($category)) {
                $forward = true;
            }
        }

        if ($forward) {
            $categoryId = current(array_keys(
                $this->_helper->getAllowedRootCategories()
            ));
            $controller->getRequest()->setParam('id', $categoryId);
            $controller->getRequest()->setParam('clear', 1);
            $this->_forward( $categoryId ? 'edit' : 'denied');
        }
    }

    /**
     * Disallow viewing categories in disallowed scopes
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogCategoryView($controller)
    {

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
            if (($this->_request->getParam('category_id') && !$this->_isCategoryAllowed($category)) ||
                !$this->_helper->getIsWebsiteLevel()) {
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
    }

    /**
     * Disallow viewing wrong catalog events or viewing them in disallowed scope
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogEventEdit($controller)
    {
        if (!$this->_request->getParam('id') && $this->_helper->getIsWebsiteLevel()) {
            return;
        }

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
     * Validate Manage Stores pages actions
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateSystemStore($controller)
    {
        // due to design of the original controller, need to run this check only once, on the first dispatch
        if (Mage::registry('enterprise_admingws_system_store_matched')) {
            return;
        }
        elseif (in_array($this->_request->getActionName(), array('save', 'newWebsite', 'newGroup', 'newStore', 'editWebsite', 'editGroup', 'editStore',
            'deleteWebsite', 'deleteWebsitePost', 'deleteGroup', 'deleteGroupPost', 'deleteStore', 'deleteStorePost'
            ))) {
            Mage::register('enterprise_admingws_system_store_matched', true, true);
        }

        switch ($this->_request->getActionName()) {
            case 'save':
                $params = $this->_request->getParams();
                if (isset($params['website'])) {
                    return $this->_forward();
                }
                if (isset($params['store']) || isset($params['group'])) {
                    if (!$this->_helper->getWebsiteIds()) {
                        return $this->_forward();
                    }
                    // preventing saving stores/groups for wrong website is handled by their models
                }
                break;
            case 'newWebsite':
                return $this->_forward();
                break;
            case 'newGroup': // break intentionally omitted
            case 'newStore':
                if (!$this->_helper->getWebsiteIds()) {
                    return $this->_forward();
                }
                break;
            case 'editWebsite':
                if (!$this->_helper->hasWebsiteAccess($this->_request->getParam('website_id'))) {
                    return $this->_forward();
                }
                break;
            case 'editGroup':
                if (!$this->_helper->hasStoreGroupAccess($this->_request->getParam('group_id'))) {
                    return $this->_forward();
                }
                break;
            case 'editStore':
                if (!$this->_helper->hasStoreAccess($this->_request->getParam('store_id'))) {
                    return $this->_forward();
                }
                break;
            case 'deleteWebsite': // break intentionally omitted
            case 'deleteWebsitePost':
                return $this->_forward();
                break;
            case 'deleteGroup': // break intentionally omitted
            case 'deleteGroupPost':
                if ($group = $this->_helper->getGroup($this->_request->getParam('item_id'))) {
                    if ($this->_helper->hasWebsiteAccess($group->getWebsiteId(), true)) {
                        return;
                    }
                }
                return $this->_forward();
                break;
            case 'deleteStore': // break intentionally omitted
            case 'deleteStorePost':
                if ($store = Mage::app()->getStore($this->_request->getParam('item_id'))) {
                    if ($this->_helper->hasWebsiteAccess($store->getWebsiteId(), true)) {
                        return;
                    }
                }
                return $this->_forward();
                break;
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
            $url = Mage::getSingleton('adminhtml/url')->getUrl('*/*/denied');
        }
        elseif (is_array($url)) {
            $url = Mage::getSingleton('adminhtml/url')->getUrl(array_shift($url), $url);
        }
        elseif (false === strpos($url, 'http', 0)) {
            $url = Mage::getSingleton('adminhtml/url')->getUrl($url);
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
