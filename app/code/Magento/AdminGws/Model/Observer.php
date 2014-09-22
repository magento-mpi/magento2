<?php
/**
 * Permissions observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model;

use Magento\Framework\Model\Exception;

class Observer extends \Magento\AdminGws\Model\Observer\AbstractObserver
{
    const ACL_WEBSITE_LEVEL = 'website';

    const ACL_STORE_LEVEL = 'store';

    /**
     * @var \Magento\Store\Model\Resource\Group\Collection
     */
    protected $_storeGroupCollection;

    /**
     * @var array
     */
    protected $_callbacks = array();

    /**
     * @var array|null
     */
    protected $_controllersMap = null;

    /**
     * @var \Magento\AdminGws\Model\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Framework\Acl\Builder
     */
    protected $_aclBuilder;

    /**
     * @var \Magento\Authorization\Model\Resource\Role\Collection
     */
    protected $_userRoles;

    /**
     * @var \Magento\Framework\Stdlib\String
     */
    protected $string;

    /**
 * @var \Magento\AdminGws\Model\CallbackInvoker
 */
    protected $callbackInvoker;

    /**
     * @param Role $role
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\Acl\Builder $aclBuilder
     * @param \Magento\Authorization\Model\Resource\Role\Collection $userRoles
     * @param \Magento\Store\Model\Resource\Group\Collection $storeGroups
     * @param ConfigInterface $config
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Stdlib\String $string
     * @param CallbackInvoker $callbackInvoker
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\AdminGws\Model\Role $role,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Acl\Builder $aclBuilder,
        \Magento\Authorization\Model\Resource\Role\Collection $userRoles,
        \Magento\Store\Model\Resource\Group\Collection $storeGroups,
        \Magento\AdminGws\Model\ConfigInterface $config,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\String $string,
        \Magento\AdminGws\Model\CallbackInvoker $callbackInvoker
    ) {
        $this->_backendAuthSession = $backendAuthSession;
        $this->_systemStore = $systemStore;
        $this->_aclBuilder = $aclBuilder;
        $this->_userRoles = $userRoles;
        $this->_storeGroupCollection = $storeGroups;
        parent::__construct($role);
        $this->config = $config;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->string = $string;
        $this->callbackInvoker = $callbackInvoker;
    }

    /**
     * Assign group/website/store permissions to the admin role
     *
     * If all permissions are allowed, all possible websites / store groups / stores will be set
     * If only websites selected, all their store groups and stores will be set as well
     *
     * @param \Magento\Authorization\Model\Role $object
     * @return void
     */
    protected function _assignRolePermissions(\Magento\Authorization\Model\Role $object)
    {
        $gwsIsAll = (bool)(int)$object->getData('gws_is_all');
        $object->setGwsIsAll($gwsIsAll);
        $notEmptyFilter = function ($el) {
            return strlen($el) > 0;
        };
        if (!is_array($object->getGwsWebsites())) {
            $object->setGwsWebsites(array_filter(explode(',', (string)$object->getGwsWebsites()), $notEmptyFilter));
        }
        if (!is_array($object->getGwsStoreGroups())) {
            $object->setGwsStoreGroups(
                array_filter(explode(',', (string)$object->getGwsStoreGroups()), $notEmptyFilter)
            );
        }

        $storeGroupIds = $object->getGwsStoreGroups();

        // set all websites and store groups
        if ($gwsIsAll) {
            $object->setGwsWebsites(array_keys($this->_storeManager->getWebsites()));
            foreach ($this->_getAllStoreGroups() as $storeGroup) {
                $storeGroupIds[] = $storeGroup->getId();
            }
        } else {
            // set selected website ids
            // set either the set store group ids or all of allowed websites
            if (empty($storeGroupIds) && count($object->getGwsWebsites())) {
                foreach ($this->_getAllStoreGroups() as $storeGroup) {
                    if (in_array($storeGroup->getWebsiteId(), $object->getGwsWebsites())) {
                        $storeGroupIds[] = $storeGroup->getId();
                    }
                }
            }
        }
        $object->setGwsStoreGroups(array_values(array_unique($storeGroupIds)));

        // determine and set store ids
        $storeIds = array();
        foreach ($this->_storeManager->getStores() as $store) {
            if (in_array($store->getGroupId(), $object->getGwsStoreGroups())) {
                $storeIds[] = $store->getId();
            }
        }
        $object->setGwsStores($storeIds);

        // set relevant website ids from allowed store group ids
        $relevantWebsites = array();
        foreach ($this->_getAllStoreGroups() as $storeGroup) {
            if (in_array($storeGroup->getId(), $object->getGwsStoreGroups())) {
                $relevantWebsites[] = $storeGroup->getWebsite()->getId();
            }
        }
        $object->setGwsRelevantWebsites(array_values(array_unique($relevantWebsites)));
    }

    /**
     * Assign websites/stores permissions data after loading admin role
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addDataAfterRoleLoad(\Magento\Framework\Event\Observer $observer)
    {
        $this->_assignRolePermissions($observer->getEvent()->getObject());
    }

    /**
     * Refresh group/website/store permissions of the current admin user's role
     *
     * @return void
     */
    public function refreshRolePermissions()
    {
        $user = $this->_backendAuthSession->getUser();
        if ($user instanceof \Magento\User\Model\User) {
            $this->_assignRolePermissions($user->getRole());
        }
    }

    /**
     * Get all store groups
     *
     * @return \Magento\Store\Model\Resource\Group\Collection
     */
    protected function _getAllStoreGroups()
    {
        return $this->_storeGroupCollection;
    }

    /**
     * Transform array of website ids and array of store group ids into comma-separated strings
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws Exception
     */
    public function setDataBeforeRoleSave($observer)
    {
        $object = $observer->getEvent()->getObject();
        $websiteIds = $object->getGwsWebsites();
        $storeGroupIds = $object->getGwsStoreGroups();

        // validate specified data
        if ($object->getGwsIsAll() === 0 && empty($websiteIds) && empty($storeGroupIds)) {
            throw new Exception(__('Please specify at least one website or one store group.'));
        }
        if (!$this->_role->getIsAll()) {
            if ($object->getGwsIsAll()) {
                throw new Exception(__('You need more permissions to set All Scopes to a Role.'));
            }
        }

        if (empty($websiteIds)) {
            $websiteIds = array();
        } else {
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', $websiteIds);
            }
            $allWebsiteIds = array_keys($this->_storeManager->getWebsites());
            foreach ($websiteIds as $websiteId) {
                if (!in_array($websiteId, $allWebsiteIds)) {
                    throw new Exception(__('Incorrect website ID: %1', $websiteId));
                }
                // prevent granting disallowed websites
                if (!$this->_role->getIsAll()) {
                    if (!$this->_role->hasWebsiteAccess($websiteId, true)) {
                        throw new Exception(
                            __(
                                'You need more permissions to access website "%1".',
                                $this->_storeManager->getWebsite($websiteId)->getName()
                            )
                        );
                    }
                }
            }
        }
        if (empty($storeGroupIds)) {
            $storeGroupIds = array();
        } else {
            if (!is_array($storeGroupIds)) {
                $storeGroupIds = explode(',', $storeGroupIds);
            }
            $allStoreGroups = array();
            foreach ($this->_storeManager->getWebsites() as $website) {
                $allStoreGroups = array_merge($allStoreGroups, $website->getGroupIds());
            }
            foreach ($storeGroupIds as $storeGroupId) {
                if (!array($storeGroupId, $allStoreGroups)) {
                    throw new Exception(__('Incorrect store ID: %1', $storeGroupId));
                }
                // prevent granting disallowed store group
                if (count(array_diff($storeGroupIds, $this->_role->getStoreGroupIds()))) {
                    throw new Exception(__('You need more permissions to save this setting.'));
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function copyWebsiteCopyPermissions($observer)
    {
        $oldWebsiteId = (string)$observer->getEvent()->getOldWebsiteId();
        $newWebsiteId = (string)$observer->getEvent()->getNewWebsiteId();
        foreach ($this->_userRoles as $role) {
            $shouldRoleBeUpdated = false;
            $roleWebsites = explode(',', $role->getGwsWebsites());
            if (!$role->getGwsIsAll() && $role->getGwsWebsites()) {
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function adminControllerPredispatch($observer)
    {
        if ($this->_backendAuthSession->isLoggedIn()) {
            // load role with true websites and store groups
            $this->_role->setAdminRole($this->_backendAuthSession->getUser()->getRole());

            if (!$this->_role->getIsAll()) {
                // disable single store mode
                $this->_storeManager->setIsSingleStoreModeAllowed(false);

                // cleanup from disallowed stores
                $this->_storeManager->reinitStores();

                // completely block some admin menu items
                $this->_denyAclLevelRules(self::ACL_WEBSITE_LEVEL);
                if (count($this->_role->getWebsiteIds()) === 0) {
                    $this->_denyAclLevelRules(self::ACL_STORE_LEVEL);
                }
                // cleanup dropdowns for forms/grids that are supposed to be built in future
                $this->_systemStore->setIsAdminScopeAllowed(false)->reload();
            }

            // inject into request predispatch to block disallowed actions
            $this->validateControllerPredispatch($observer);
        }
    }

    /**
     * Check access to massaction status block
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function catalogProductPrepareMassAction($observer)
    {
        if ($this->_role->getIsAll()) {
            return $this;
        }

        $storeCode = $this->_request->getParam('store');
        $storeId = $storeCode ? $this->_storeManager->getStore(
            $storeCode
        )->getId() : \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        if ($this->_role->hasStoreAccess($storeId)) {
            return $this;
        }

        $massActionBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
        $massActionBlock->removeItem('status');
        $massActionBlock->removeItem('attributes');

        return $this;
    }

    /**
     * Deny acl level rules.
     *
     * @param string $level
     * @return $this
     */
    protected function _denyAclLevelRules($level)
    {
        foreach ($this->config->getDeniedAclResources($level) as $rule) {
            $this->_aclBuilder->getAcl()->deny($this->_backendAuthSession->getUser()->getAclRole(), $rule);
        }
        return $this;
    }

    /**
     * Limit a collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function limitCollection($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }
        $collection = $observer->getEvent()->getCollection();
        if (!($callback = $this->config->getCallbackForObject($collection, 'collection_load_before'))) {
            return;
        }
        $this->callbackInvoker->invoke($callback, $this->config->getGroupProcessor('collection_load_before'), $collection);
    }

    /**
     * Validate / update a model before saving it
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function validateModelSaveBefore($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }
        $model = $observer->getEvent()->getObject();
        if (!($callback = $this->config->getCallbackForObject($model, 'model_save_before'))) {
            return;
        }
        $this->callbackInvoker->invoke($callback, $this->config->getGroupProcessor('model_save_before'), $model);
    }

    /**
     * Initialize a model after loading it
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function validateModelLoadAfter($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }
        $model = $observer->getEvent()->getObject();
        if (!($callback = $this->config->getCallbackForObject($model, 'model_load_after'))) {
            return;
        }
        $this->callbackInvoker->invoke($callback, $this->config->getGroupProcessor('model_load_after'), $model);
    }

    /**
     * Validate a model before delete
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function validateModelDeleteBefore($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }

        $model = $observer->getEvent()->getObject();
        if (!($callback = $this->config->getCallbackForObject($model, 'model_delete_before'))) {
            return;
        }
        $this->callbackInvoker->invoke($callback, $this->config->getGroupProcessor('model_delete_before'), $model);
    }

    /**
     * Validate page by current request (module, controller, action)
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function validateControllerPredispatch($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }

        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getEvent()->getRequest();
        // initialize controllers map
        if (null === $this->_controllersMap) {
            $this->_controllersMap = array('full' => array(), 'partial' => array());
            foreach ($this->config->getCallbacks('controller_predispatch') as $actionName => $method) {
                list($module, $controller, $action) = explode('__', $actionName);
                if ($action) {
                    $this->_controllersMap['full'][$module][$controller][$action] = $method;
                } else {
                    $this->_controllersMap['partial'][$module][$controller] = $method;
                }
            }
        }

        // map request to validator callback
        $routeName = $request->getRouteName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        $callback = false;
        if (isset(
            $this->_controllersMap['full'][$routeName]
        ) && isset(
            $this->_controllersMap['full'][$routeName][$controllerName]
        ) && isset(
            $this->_controllersMap['full'][$routeName][$controllerName][$actionName]
        )
        ) {
            $callback = $this->_controllersMap['full'][$routeName][$controllerName][$actionName];
        } elseif (isset(
            $this->_controllersMap['partial'][$routeName]
        ) && isset(
            $this->_controllersMap['partial'][$routeName][$controllerName]
        )
        ) {
            $callback = $this->_controllersMap['partial'][$routeName][$controllerName];
        }

        if ($callback) {
            $this->callbackInvoker->invoke(
                $callback,
                $this->config->getGroupProcessor('controller_predispatch'),
                $observer->getEvent()->getControllerAction()
            );
        }
    }

    /**
     * Apply restrictions to misc blocks before html
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function restrictBlocks($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }
        if (!($block = $observer->getEvent()->getBlock())) {
            return;
        }
        if (!($callback = $this->config->getCallbackForObject($block, 'block_html_before'))) {
            return;
        }
        /* the $observer is used intentionally */
        $this->callbackInvoker->invoke($callback, $this->config->getGroupProcessor('block_html_before'), $observer);
    }

    /**
     * Update store list which is available for role
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function updateRoleStores($observer)
    {
        $this->_role->setStoreIds(
            array_merge($this->_role->getStoreIds(), array($observer->getStore()->getStoreId()))
        );
        return $this;
    }
}
