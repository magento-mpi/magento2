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
 * Permissions observer
 *
 */
class Enterprise_AdminGws_Model_Observer
{
    const XML_PATH_ACL_DENY_RULES = 'adminhtml/enterprise/admingws/acl/deny';
    const XML_PATH_VALIDATE_CALLBACK = 'adminhtml/enterprise/admingws/';

    const VALIDATE_COLLECTIONS = 'collections';
    const VALIDATE_MODELS = 'models';
    const VALIDATE_CONTROLLERS = 'controllers';

    /**
     * @var Mage_Core_Model_Mysql4_Store_Group_Collection
     */
    protected $_storeGroupCollection;
    protected $_callbacks      = array();
    protected $_controllersMap = null;

    /**
     * Put websites/stores permissions data after loading admin role
     *
     * If all permissions are allowed, all possible websites / store groups / stores will be set
     * If only websites selected, all their store groups and stores will be set as well
     *
     * @param  Varien_Event_Observer $observer
     * @return Enterprise_Permissions_Model_Observer
     */
    public function addDataAfterRoleLoad($observer)
    {
        $object   = $observer->getEvent()->getObject();
        $gwsIsAll = (bool)(int)$object->getData('gws_is_all');
        $object->setGwsIsAll($gwsIsAll);

        $storeGroupIds = array();

        // set all websites and store groups
        if ($gwsIsAll) {
            $object->setGwsWebsites(array_keys(Mage::app()->getWebsites()));
            foreach ($this->_getAllStoreGroups() as $storeGroup) {
                $storeGroupIds[] = $storeGroup->getId();
            }
            $object->setGwsStoreGroups($storeGroupIds);
        }
        else {
            // set selected website ids
            $websiteIds = ($object->getData('gws_websites') != '' ? explode(',', $object->getData('gws_websites')) : array());
            $object->setGwsWebsites($websiteIds);

            // set either the set store group ids or all of allowed websites
            if ($object->getData('gws_store_groups') != '') {
                $storeGroupIds = explode(',', $object->getData('gws_store_groups'));
            }
            else {
                if ($websiteIds) {
                    foreach ($this->_getAllStoreGroups() as $storeGroup) {
                        if (in_array($storeGroup->getWebsiteId(), $websiteIds)) {
                            $storeGroupIds[] = $storeGroup->getId();
                        }
                    }
                }
            }
            $object->setGwsStoreGroups($storeGroupIds);
        }

        // determine and set store ids
        $storeIds = array();
        foreach (Mage::app()->getStores() as $store) {
            if (in_array($store->getGroupId(), $storeGroupIds)) {
                $storeIds[] = $store->getId();
            }
        }
        $object->setGwsStores($storeIds);

        // set relevant website ids from allowed store group ids
        $relevantWebsites = array();
        foreach ($this->_getAllStoreGroups() as $storeGroup) {
            if (in_array($storeGroup->getId(), $storeGroupIds)) {
                $relevantWebsites[] = $storeGroup->getWebsite()->getId();
            }
        }
        $object->setGwsRelevantWebsites(array_values(array_unique($relevantWebsites)));

        return $this;
    }

    /**
     * Get all store groups
     *
     * @return Mage_Core_Model_Mysql4_Store_Group_Collection
     */
    protected function _getAllStoreGroups()
    {
        if (null === $this->_storeGroupCollection) {
            $this->_storeGroupCollection = Mage::getResourceSingleton('core/store_group_collection');
        }
        return $this->_storeGroupCollection;
    }

    /**
     * Transform array of website ids and array of store group ids into comma-separated strings
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Permissions_Model_Observer
     */
    public function setDataBeforeRoleSave($observer)
    {
        $object = $observer->getEvent()->getObject();
        $websiteIds    = $object->getGwsWebsites();
        $storeGroupIds = $object->getGwsStoreGroups();
        /* @var $helper Enterprise_AdminGws_Helper_Data */
        $helper = Mage::helper('enterprise_admingws');

        // validate specified data
        if ($object->getGwsIsAll() == 0 && empty($websiteIds) && empty($storeGroupIds)) {
            Mage::throwException(Mage::helper('enterprise_admingws')->__('Specify at least one website or one store group.'));
        }
        if (empty($websiteIds)) {
            $websiteIds = array();
        }
        else {
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', $websiteIds);
            }
            $allWebsiteIds = array_keys(Mage::app()->getWebsites());
            foreach ($websiteIds as $websiteId) {
                if (!in_array($websiteId, $allWebsiteIds)) {
                    Mage::throwException(Mage::helper('enterprise_admingws')->__('Wrong website ID: %d', $websiteId));
                }
                // prevent from granting disallowed websites
                if (!$helper->getIsAll()) {
                    if (!in_array($websiteId, $helper->getWebsiteIds())) {
                        Mage::throwException(Mage::helper('enterprise_admingws')->__('Website "%s" is not allowed in your current permission scope.', Mage::app()->getWebsite($websiteId)->getName()));
                    }
                }
            }
        }
        if (empty($storeGroupIds)) {
            $storeGroupIds = array();
        }
        else {
            if (!is_array($storeGroupIds)) {
                $storeGroupIds = explode(',', $storeGroupIds);
            }
            $allStoreGroups = array();
            foreach (Mage::app()->getWebsites() as $website) {
                $allStoreGroups = array_merge($allStoreGroups, $website->getGroupIds());
            }
            foreach ($storeGroupIds as $storeGroupId) {
                if (!array($storeGroupId, $allStoreGroups)) {
                    Mage::throwException(Mage::helper('enterprise_admingws')->__('Wrong store ID: %d', $storeGroupId));
                }
            }
        }

        $object->setGwsWebsites(implode(',', $websiteIds));
        $object->setGwsStoreGroups(implode(',', $storeGroupIds));

        return $this;
    }

    /**
     * Prepare role object permissions data before saving
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Permissions_Model_Observer
     */
    public function prepareRoleSave($observer)
    {
        $object = $observer->getEvent()->getObject();
        $request = $observer->getEvent()->getRequest();

        $isAll = (int)$request->getPost('gws_is_all');
        $websiteIds = (array)$request->getPost('gws_websites');
        $storeGroupIds = (array)$request->getPost('gws_store_groups');

        $object->setGwsIsAll($isAll);
        if (!$isAll) {
            $object->setGwsWebsites($websiteIds)->setGwsStoreGroups($storeGroupIds);
        }
        return $this;
    }

    /**
     * Copy permission scopes to new specified website
     *
     * @param Varien_Event_Observer $observer
     */
    public function copyWebsiteCopyPermissions($observer)
    {
        $oldWebsiteId = (string)$observer->getOldWebsiteId();
        $newWebsiteId = (string)$observer->getNewWebsiteId();
        $roles = Mage::getResourceSingleton('admin/roles_collection');
        foreach ($roles as $role) {
            $shouldRoleBeUpdated = false;
            $roleWebsites = explode(',', $role->getGwsWebsites());
            if ((!$role->getGwsIsAll()) && $role->getGwsWebsites()) {
                if (in_array($oldWebsiteId, $roleWebsites)) {
                    $roleWebsites[] = $newWebsiteId;
                    $shouldRoleBeUpdated = true;
                }
            }
            if ($shouldRoleBeUpdated) {
                $role->setGwsWebsites(implode(',', $roleWebsites));
                $role->save();
            }
        }
    }

    /**
     * Reinit stores only with allowed scopes
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminControllerPredispatch($observer)
    {
        /* @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');

        if ($session->isLoggedIn()) {
            // load role with true websites and store groups
            Mage::helper('enterprise_admingws')->setRole(Mage::getSingleton('admin/session')->getUser()->getRole());
            // reset websites/stores
            Mage::app()->reinitStores();

            if (!Mage::helper('enterprise_admingws')->getIsAll()) {
                foreach (Mage::getConfig()->getNode(self::XML_PATH_ACL_DENY_RULES)->children() as $rule) {
                    $session->getAcl()->deny($session->getUser()->getAclRole(), $rule);
                }
            }

            $this->validateControllerPredispatch($observer);
        }
    }

    /**
     * Limit a collection
     *
     * @param Varien_Event_Observer $observer
     */
    public function limitCollection($observer)
    {
        if (Mage::helper('enterprise_admingws')->getIsAll()) {
            return;
        }
        $collection = $observer->getCollection();
        if (!$callback = $this->_pickCallback('collections', $collection)) {
            return;
        }
        Mage::getSingleton('enterprise_admingws/collections')->$callback($collection);
    }

    /**
     * Validate / update a model before saving it
     *
     * @param unknown_type $observer
     */
    public function validateModelBeforeSave($observer)
    {
        if (Mage::helper('enterprise_admingws')->getIsAll()) {
            return;
        }
        $model = $observer->getObject();
        if (!$callback = $this->_pickCallback('models_before_save', $model)) {
            return;
        }
        Mage::getSingleton('enterprise_admingws/models')->$callback($model);
    }

    /**
     * Initialize a model after loading it
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function validateModelAfterLoad($observer)
    {
        if (Mage::helper('enterprise_admingws')->getIsAll()) {
            return;
        }
        $model = $observer->getObject();
        if (!$callback = $this->_pickCallback('models_after_load', $model)) {
            return;
        }
        Mage::getSingleton('enterprise_admingws/models')->$callback($model);
    }

    /**
     * Validate a model before delete
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function validateModelBeforeDelete($observer)
    {
        if (Mage::helper('enterprise_admingws')->getIsAll()) {
            return;
        }

        $model = $observer->getObject();
        if (!$callback = $this->_pickCallback('models_before_delete', $model)) {
            return;
        }

        Mage::getSingleton('enterprise_admingws/models')->$callback($model);
    }


    /**
     * Validate category before move
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function validateCatalogCategoryMoveBefore($observer)
    {
        $parentCategory = $observer->getEvent()->getParent();
        $currentCategory = $observer->getEvent()->getCategory();

        foreach (array($parentCategory, $currentCategory) as $category) {
            if (!Mage::helper('enterprise_admingws')->hasExclusiveCategoryAccess($category->getPath())) {
                Mage::throwException(
                    Mage::helper('enterprise_admingws')->__('You cannot move this category')
                );
            }
        }
    }

    /**
     * Validate category moveable
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function validateCatalogCategoryMoveable($observer)
    {
        $category = $observer->getEvent()->getOptions()->getCategory();
        if (!Mage::helper('enterprise_admingws')
                ->hasExclusiveCategoryAccess($category->getData('path'))) {

            $observer->getEvent()->getOptions()->setIsMoveable(false);
        }
    }

    /**
     * Validate add new category action
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function validateCatalogAddCategory($observer)
    {
        if (!Mage::helper('enterprise_admingws')->getIsAll()) {
            $observer->getEvent()->getOptions()->setIsAllow(false);
        }
    }


    /**
     * Validate category permissions tab
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function validateCatalogPermissionsIsActiveCategory($observer)
    {
        if (!Mage::helper('enterprise_admingws')->getIsAll()
            && !Mage::helper('enterprise_admingws')->hasExclusiveCategoryAccess(
                            $observer->getEvent()->getOptions()->getCategory()
                                ->getPath())) {
            $observer->getEvent()->getOptions()->setIsAllowed(false);
        }
    }

    /**
     * Limit admin website in dropdowns
     *
     * @param Varien_Event_Observer $observer
     */
    public function limitAdminWebsiteInDropdowns($observer)
    {
        if (!Mage::helper('enterprise_admingws')->getIsAll()) {
            $observer->getEvent()->getOptions()->setDisable(true);
        }
    }

    /**
     * Validate page by current request (module, controller, action)
     *
     * @param Varien_Event_Observer $observer
     */
    public function validateControllerPredispatch($observer)
    {
        if (Mage::helper('enterprise_admingws')->getIsAll()) {
            return;
        }

        // initialize controllers map
        if (null === $this->_controllersMap) {
            $this->_controllersMap = array('full' => array(), 'partial' => array());
            foreach (Mage::getConfig()->getNode(self::XML_PATH_VALIDATE_CALLBACK . self::VALIDATE_CONTROLLERS)->children() as $actionName => $method) {
                list($module, $controller, $action) = explode('__', $actionName);
                $map = array('module' => $module, 'controller' => $controller, 'action' => $action, 'callback' => $method);
                if ($action) {
                    $this->_controllersMap['full'][$module][$controller][$action] = (string)$method;
                }
                else {
                    $this->_controllersMap['partial'][$module][$controller] = (string)$method;
                }
            }
        }

        // map request to validator callback
        $request        = Mage::app()->getRequest();
        $routeName      = $request->getRouteName();
        $controllerName = $request->getControllerName();
        $actionName     = $request->getActionName();
        $callback       = false;
        if (isset($this->_controllersMap['full'][$routeName])
            && isset($this->_controllersMap['full'][$routeName][$controllerName])
            && isset($this->_controllersMap['full'][$routeName][$controllerName][$actionName])) {
            $callback = $this->_controllersMap['full'][$routeName][$controllerName][$actionName];
        }
        elseif (isset($this->_controllersMap['partial'][$routeName])
            && isset($this->_controllersMap['partial'][$routeName][$controllerName])) {
            $callback = $this->_controllersMap['partial'][$routeName][$controllerName];
        }

        if ($callback) {
            Mage::getSingleton('enterprise_admingws/controllers')->$callback($observer->getControllerAction());
        }
    }

    /**
     * Get a limiter callback for an instance from mappers configuration
     *
     * @param string $callbackGroup (collection, model)
     * @param object $instance
     * @return string
     */
    public function _pickCallback($callbackGroup, $instance)
    {
        if (!$instanceClass = get_class($instance)) {
            return;
        }

        // gather callbacks from mapper configuration
        if (!isset($this->_callbacks[$callbackGroup])) {
            $this->_callbacks[$callbackGroup] = array();
            foreach ((array)Mage::getConfig()->getNode(self::XML_PATH_VALIDATE_CALLBACK . $callbackGroup) as $className => $method) {
                $factoryClassName = str_replace('__', '/', $className);
                if (self::VALIDATE_COLLECTIONS === $callbackGroup) {
                    if (0 === strpos($factoryClassName, '_', 0)) {
                        $className = Mage::getConfig()->getModelClassName(substr($factoryClassName, 1));
                    }
                    else {
                        $className = Mage::getConfig()->getResourceModelClassName($factoryClassName);
                    }
                }
                else {
                    $className = Mage::getConfig()->getModelClassName($factoryClassName);
                }
                if (class_exists($className)) {
                    $this->_callbacks[$callbackGroup][$className] = $method;
                }
            }
        }

        /**
         * Determine callback for current instance
         * Explicit class name has priority before inherited classes
         */
        $callback = false;
        if (isset($this->_callbacks[$callbackGroup][$instanceClass])) {
            $callback = $this->_callbacks[$callbackGroup][$instanceClass];
        }
        else {
            foreach ($this->_callbacks[$callbackGroup] as $className => $method) {
                if ($instance instanceof $className) {
                    $callback = $method;
                    break;
                }
            }
        }
        return $callback;
    }

}
