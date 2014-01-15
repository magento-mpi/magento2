<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controllers AdminGws validator
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdminGws\Model;

class Controllers extends \Magento\AdminGws\Model\Observer\AbstractObserver
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var bool
     */
    protected $_isForwarded = false;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    private $_storeManager = null;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_response = null;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\AdminGws\Model\Resource\CollectionsFactory
     */
    protected $_collectionsFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\ProductFactory
     */
    protected $_productFactoryRes;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\AdminGws\Model\Role $role
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\AdminGws\Model\Resource\CollectionsFactory $collectionsFactory
     * @param \Magento\Catalog\Model\Resource\ProductFactory $productFactoryRes
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\App\ActionFlag $actionFlag
     * @param \Magento\Message\ManagerInterface $messageManager
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\AdminGws\Model\Role $role,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\AdminGws\Model\Resource\CollectionsFactory $collectionsFactory,
        \Magento\Catalog\Model\Resource\ProductFactory $productFactoryRes,
        \Magento\Core\Model\Registry $registry,
        \Magento\App\RequestInterface $request,
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ResponseInterface $response,
        \Magento\App\ActionFlag $actionFlag,
        \Magento\Message\ManagerInterface $messageManager
    ) {
        $this->_registry = $registry;
        $this->_backendUrl = $backendUrl;
        $this->_backendSession = $backendSession;
        $this->_collectionsFactory = $collectionsFactory;
        $this->_productFactoryRes = $productFactoryRes;
        $this->_actionFlag = $actionFlag;
        $this->_objectManager = $objectManager;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
        $this->messageManager = $messageManager;
        parent::__construct($role);
    }

    /**
     * Make sure the System Configuration pages are used in proper scopes
     *
     * @param \Magento\Backend\App\Action $controller
     */
    public function validateSystemConfig($controller)
    {
        // allow specific store view scope
        $storeCode = $this->_request->getParam('store');
        if ($storeCode) {
            $store = $this->_storeManager->getStore($storeCode);
            if ($store) {
                if ($this->_role->hasStoreAccess($store->getId())) {
                    return;
                }
            }
        } elseif ($websiteCode = $this->_request->getParam('website')) {
            try {
                $website = $this->_storeManager->getWebsite($websiteCode);
                if ($website) {
                    if ($this->_role->hasWebsiteAccess($website->getId(), true)) {
                        return;
                    }
                }
            }
            catch (\Magento\Core\Exception $e) {
                // redirect later from non-existing website
            }
        }

        // redirect to first allowed website or store scope
        if ($this->_role->getWebsiteIds()) {
            return $this->_redirect($this->_backendUrl->getUrl(
                    'adminhtml/system_config/edit',
                    array('website' => $this->_storeManager->getAnyStoreView()->getWebsite()->getCode())
                )
            );
        }
        $this->_redirect($this->_backendUrl->getUrl(
                'adminhtml/system_config/edit',
                array(
                    'website' => $this->_storeManager->getAnyStoreView()->getWebsite()->getCode(),
                    'store' => $this->_storeManager->getAnyStoreView()->getCode())
                )
        );
    }

    /**
     * Validate misc catalog product requests
     */
    public function validateCatalogProduct()
    {
        if (!$this->validateNoWebsiteGeneric(array('new', 'delete', 'duplicate'))) {
            return;
        }
    }

    /**
     * Validate catalog product edit page
     *
     * @param \Magento\Backend\App\Action $controller
     */
    public function validateCatalogProductEdit($controller)
    {
        // redirect from disallowed scope
        if (!$this->_isAllowedStoreInRequest()) {
            return $this->_redirect(array('*/*/*', 'id' => $this->_request->getParam('id')));
        }
    }

    /**
     * Validate catalog product review save, edit action
     *
     * @param \Magento\Backend\App\Action $controller
     */
    public function validateCatalogProductReview($controller)
    {
        $reviewStores = $this->_objectManager->create('Magento\Review\Model\Review')
            ->load($controller->getRequest()->getParam('id'))
            ->getStores();

        $storeIds = $this->_role->getStoreIds();

        $allowedIds = array_intersect($reviewStores, $storeIds);
        if (empty($allowedIds)) {
            $this->_redirect();
        }
    }

    /**
     * Validate catalog product massStatus
     */
    public function validateCatalogProductMassActions()
    {
        if ($this->_role->getIsAll()) {
            return;
        }

        $store = $this->_storeManager->getStore(
            $this->_request->getParam('store', \Magento\Core\Model\Store::DEFAULT_STORE_ID)
        );
        if (!$this->_role->hasStoreAccess($store->getId())) {
            $this->_forward();
        }
    }

    /**
     * Avoid viewing disallowed customer
     */
    public function validateCustomerEdit()
    {
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')
            ->load($this->_request->getParam('id'));
        if ($customer->getId() && !in_array($customer->getWebsiteId(), $this->_role->getRelevantWebsiteIds())) {
            return $this->_forward();
        }
    }

    /**
     * Avoid viewing disallowed customer balance
     */
    public function validateCustomerbalance()
    {
        if (!$id = $this->_request->getParam('id')) {
            return $this->_forward();
        }
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($id);
        if ((!$customer->getId()) || !in_array($customer->getWebsiteId(), $this->_role->getRelevantWebsiteIds())) {
            return $this->_forward();
        }
    }

    /**
     * Disallow submitting gift cards without website-level permissions
     *
     * @param \Magento\Backend\App\Action $controller
     */
    public function validateGiftCardAccount($controller)
    {
        $controller->setShowCodePoolStatusMessage(false);
        if (!$this->_role->getIsWebsiteLevel()) {
            $action = $this->_request->getActionName();
            if (in_array($action, array('new', 'generate'))
                || $action == 'edit' && !$this->_request->getParam('id')) {
                return $this->_forward();
            }
        }
    }

    /**
     * Prevent viewing wrong categories and creation pages
     *
     * @param \Magento\Backend\App\Action $controller
     */
    public function validateCatalogCategories($controller)
    {
        $forward = false;
        switch ($controller->getRequest()->getActionName()) {
            case 'add':
                /**
                 * adding is not allowed from beginning if user has scope specified permissions
                 */
                $forward = true;
                $parentId = $controller->getRequest()->getParam('parent');
                if ($parentId) {
                    $forward = !$this->_validateCatalogSubCategoryAddPermission($parentId);
                }
                break;
            case 'edit':
                if (!$controller->getRequest()->getParam('id')) {
                    $parentId = $controller->getRequest()->getParam('parent');
                    if ($parentId) {
                        $forward = !$this->_validateCatalogSubCategoryAddPermission($parentId);
                    } else {
                        $forward = true; // no adding root categories
                    }
                } else {
                    $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($controller->getRequest()->getParam('id'));
                    if (!$category->getId() || !$this->_isCategoryAllowed($category)) {
                        $forward = true; // no viewing wrong categories
                    }
                }
                break;
        }

        // forward to first allowed root category
        if ($forward) {
            $firstRootId = current(array_keys($this->_role->getAllowedRootCategories()));
            if ($firstRootId) {
                $controller->getRequest()->setParam('id', $firstRootId);
                $controller->getRequest()->setParam('clear', 1);
                return $this->_forward('edit');
            }
            $this->_forward();
        }
    }

    /**
     * Disallow viewing categories in disallowed scopes
     *
     * @param \Magento\Backend\App\Action $controller
     */
    public function validateCatalogCategoryView($controller)
    {

    }

    /**
     * Disallow submitting catalog event in wrong scope
     */
    public function validateCatalogEvents()
    {
        // instead of generic (we are capped by allowed store groups root categories)
        // check whether attempting to create event for wrong category
        if ('new' === $this->_request->getActionName()) {
            $category = $this->_objectManager->create('Magento\Catalog\Model\Category')
                ->load($this->_request->getParam('category_id'));
            if (($this->_request->getParam('category_id') && !$this->_isCategoryAllowed($category)) ||
                !$this->_role->getIsWebsiteLevel()) {
                return $this->_forward();
            }
        }
    }

    /**
     * Disallow viewing wrong catalog events or viewing them in disallowed scope
     *
     * @param \Magento\Backend\App\Action $controller
     */
    public function validateCatalogEventEdit($controller)
    {
        if (!$this->_request->getParam('id') && $this->_role->getIsWebsiteLevel()) {
            return;
        }

        // avoid viewing disallowed events
        $catalogEvent = $this->_objectManager->create('Magento\CatalogEvent\Model\Event')
            ->load($this->_request->getParam('id'));
        $category     = $this->_objectManager->create('Magento\Catalog\Model\Category')
            ->load($catalogEvent->getCategoryId());
        if (!$this->_isCategoryAllowed($category)) {
            return $this->_forward();
        }

        // redirect from disallowed store scope
        if (!$this->_isAllowedStoreInRequest()) {
            return $this->_redirect(
                array(
                    '*/*/*', 'store' => $this->_storeManager->getAnyStoreView()->getId(),
                    'id' => $catalogEvent->getId()
                )
            );
        }
    }

    /**
     * Disallow any creation order activity, if there is no website-level access
     *
     * @param \Magento\Backend\App\Action $controller
     */
    public function validateSalesOrderCreation($controller)
    {
        if (!$this->_role->getWebsiteIds()) {
            return $this->_forward();
        }

         // check whether there is disallowed website in request?
    }

// TODO allow viewing sales information only from allowed websites

    /**
     * Don't allow to create or delete entity, if there is no website permissions
     *
     * Returns false if disallowed
     *
     * @param string|array $denyActions
     * @param string $saveAction
     * @param string $idFieldName
     * @return bool
     */
    public function validateNoWebsiteGeneric(
        $denyActions = array('new', 'delete'), $saveAction = 'save', $idFieldName = 'id'
    ) {
        if (!is_array($denyActions)) {
            $denyActions = array($denyActions);
        }
        if ((!$this->_role->getWebsiteIds()) && (in_array($this->_request->getActionName(), $denyActions)
            || ($saveAction === $this->_request->getActionName() && 0 == $this->_request->getParam($idFieldName)))) {
            $this->_forward();
            return false;
        }
        return true;
    }

    /**
     * Validate Manage Stores pages actions
     */
    public function validateSystemStore()
    {
        // due to design of the original controller, need to run this check only once, on the first dispatch
        if ($this->_registry->registry('magento_admingws_system_store_matched')) {
            return;
        } elseif (in_array($this->_request->getActionName(), array('save', 'newWebsite', 'newGroup', 'newStore',
            'editWebsite', 'editGroup', 'editStore', 'deleteWebsite', 'deleteWebsitePost', 'deleteGroup',
            'deleteGroupPost', 'deleteStore', 'deleteStorePost'
            ))) {
            $this->_registry->register('magento_admingws_system_store_matched', true, true);
        }

        switch ($this->_request->getActionName()) {
            case 'save':
                $params = $this->_request->getParams();
                if (isset($params['website'])) {
                    return $this->_forward();
                }
                if (isset($params['store']) || isset($params['group'])) {
                    if (!$this->_role->getWebsiteIds()) {
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
                if (!$this->_role->getWebsiteIds()) {
                    return $this->_forward();
                }
                break;
            case 'editWebsite':
                if (!$this->_role->hasWebsiteAccess($this->_request->getParam('website_id'))) {
                    return $this->_forward();
                }
                break;
            case 'editGroup':
                if (!$this->_role->hasStoreGroupAccess($this->_request->getParam('group_id'))) {
                    return $this->_forward();
                }
                break;
            case 'editStore':
                if (!$this->_role->hasStoreAccess($this->_request->getParam('store_id'))) {
                    return $this->_forward();
                }
                break;
            case 'deleteWebsite': // break intentionally omitted
            case 'deleteWebsitePost':
                return $this->_forward();
                break;
            case 'deleteGroup': // break intentionally omitted
            case 'deleteGroupPost':
                $group = $this->_role->getGroup($this->_request->getParam('item_id'));
                if ($group) {
                    if ($this->_role->hasWebsiteAccess($group->getWebsiteId(), true)) {
                        return;
                    }
                }
                return $this->_forward();
                break;
            case 'deleteStore': // break intentionally omitted
            case 'deleteStorePost':
                $store = $this->_storeManager->getStore($this->_request->getParam('item_id'));
                if ($store) {
                    if ($this->_role->hasWebsiteAccess($store->getWebsiteId(), true)) {
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
     * @param array|string $url
     */
    protected function _redirect($url = null)
    {
        $this->_actionFlag->set('', \Magento\App\Action\Action::FLAG_NO_DISPATCH, true);
        if (null === $url) {
            $url = $this->_backendUrl->getUrl('adminhtml/*/denied');
        } elseif (is_array($url)) {
            $url = $this->_backendUrl->getUrl(array_shift($url), $url);
        } elseif (false === strpos($url, 'http', 0)) {
            $url = $this->_backendUrl->getUrl($url);
        }
        $this->_response->setRedirect($url);
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
            && (null === $controller || $this->_request->getControllerName() === $controller)
        ) {
            return;
        }

        $this->_request->initForward();

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
    protected function _isAllowedStoreInRequest($idFieldName = 'store')
    {
        $storeId = $this->_request->getParam($idFieldName);
        if (empty($storeId)) {
            return true;
        }
        $store = $this->_storeManager->getStore($storeId);
        return $this->_role->hasStoreAccess($store->getId());
    }

    /**
     * Check whether specified category is allowed
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return bool
     */
    protected function _isCategoryAllowed($category)
    {
        if (!$category->getId()) {
            return false;
        }
        $categoryPath = $category->getPath();
        foreach ($this->_role->getAllowedRootCategories() as $rootPath) {
            if ($categoryPath === $rootPath || 0 === strpos($categoryPath, "{$rootPath}/")) {
                return true;
            }
        }
        return false;
    }

    /**
     * Validate Order view actions
     *
     * @return bool
     */
    public function validateSalesOrderViewAction()
    {
        $id = $this->_request->getParam('order_id');
        if ($id) {
            $object = $this->_objectManager->create('Magento\Sales\Model\Order')->load($id);
            if ($object && $object->getId()) {
                $store = $object->getStoreId();
                if (!$this->_role->hasStoreAccess($store)) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate Creditmemo view actions
     *
     * @return bool
     */
    public function validateSalesOrderCreditmemoViewAction()
    {
        $id = $this->_request->getParam('creditmemo_id');
        if (!$id) {
            $id = $this->_request->getParam('id');
        }
        if ($id) {
            $object = $this->_objectManager->create('Magento\Sales\Model\Order\Creditmemo')->load($id);
            if ($object && $object->getId()) {
                $store = $object->getStoreId();
                if (!$this->_role->hasStoreAccess($store)) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate Invoice view actions
     *
     * @return bool
     */
    public function validateSalesOrderInvoiceViewAction()
    {
        $id = $this->_request->getParam('invoice_id');
        if (!$id) {
            $id = $this->_request->getParam('id');
        }
        if ($id) {
            $object = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice')->load($id);
            if ($object && $object->getId()) {
                $store = $object->getStoreId();
                if (!$this->_role->hasStoreAccess($store)) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate Shipment view actions
     *
     * @return bool
     */
    public function validateSalesOrderShipmentViewAction()
    {
        $id = $this->_request->getParam('shipment_id');
        if (!$id) {
            $id = $this->_request->getParam('id');
        }
        if ($id) {
            $object = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($id);
            if ($object && $object->getId()) {
                $store = $object->getStoreId();
                if (!$this->_role->hasStoreAccess($store)) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate Creditmemo creation actions
     *
     * @return bool
     */
    public function validateSalesOrderCreditmemoCreateAction()
    {
        if ($id = $this->_request->getParam('order_id')) {
            $className = 'Magento\Sales\Model\Order';
        } else if ($id = $this->_request->getParam('invoice_id')) {
            $className = 'Magento\Sales\Model\Order\Invoice';
        } else if ($id = $this->_request->getParam('creditmemo_id')) {
            $className = 'Magento\Sales\Model\Order\Creditmemo';
        } else {
            return true;
        }

        if ($id) {
            $object = $this->_objectManager->create($className)->load($id);
            if ($object && $object->getId()) {
                $store = $object->getStoreId();
                if (!$this->_role->hasStoreAccess($store)) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate Invoice creation actions
     *
     * @return bool
     */
    public function validateSalesOrderInvoiceCreateAction()
    {
        if ($id = $this->_request->getParam('order_id')) {
            $className = 'Magento\Sales\Model\Order';
        } else if ($id = $this->_request->getParam('invoice_id')) {
            $className = 'Magento\Sales\Model\Order\Invoice';
        } else {
            return true;
        }

        if ($id) {
            $object = $this->_objectManager->create($className)->load($id);
            if ($object && $object->getId()) {
                $store = $object->getStoreId();
                if (!$this->_role->hasStoreAccess($store)) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate Shipment creation actions
     *
     * @return bool
     */
    public function validateSalesOrderShipmentCreateAction()
    {
        if ($id = $this->_request->getParam('order_id')) {
            $className = 'Magento\Sales\Model\Order';
        } else if ($id = $this->_request->getParam('shipment_id')) {
            $className = 'Magento\Sales\Model\Order\Shipment';
        } else {
            return true;
        }

        if ($id) {
            $object = $this->_objectManager->create($className)->load($id);
            if ($object && $object->getId()) {
                $store = $object->getStoreId();
                if (!$this->_role->hasStoreAccess($store)) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate Order mass actions
     *
     * @return bool
     */
    public function validateSalesOrderMassAction()
    {
        $ids = $this->_request->getParam('order_ids', array());
        if ($ids) {
            if ($ids && is_array($ids)) {
                foreach ($ids as $id) {
                    $object = $this->_objectManager->create('Magento\Sales\Model\Order')->load($id);
                    if ($object && $object->getId()) {
                        $store = $object->getStoreId();
                        if (!$this->_role->hasStoreAccess($store)) {
                            $this->_forward();
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Validate Order edit action
     *
     * @return bool
     */
    public function validateSalesOrderEditStartAction()
    {
        $id = $this->_request->getParam('order_id');
        if ($id) {
            $object = $this->_objectManager->create('Magento\Sales\Model\Order')->load($id);
            if ($object && $object->getId()) {
                $store = $object->getStoreId();
                if (!$this->_role->hasStoreAccess($store)) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate Shipment tracking actions
     *
     * @return bool
     */
    public function validateSalesOrderShipmentTrackAction()
    {
        $id = $this->_request->getParam('track_id');
        if ($id) {
            $object = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment\Track')->load($id);
            if ($object && $object->getId()) {
                $store = $object->getStoreId();
                if (!$this->_role->hasStoreAccess($store)) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return $this->validateSalesOrderShipmentCreateAction();
    }

    /**
     * Validate Terms and Conditions management edit action
     *
     * @return bool
     */
    public function validateCheckoutAgreementEditAction()
    {
        $id = $this->_request->getParam('id');
        if ($id) {
            $object = $this->_objectManager->create('Magento\Checkout\Model\Agreement')->load($id);
            if ($object && $object->getId()) {
                $stores = $object->getStoreId();
                foreach ($stores as $store) {
                    if ($store == 0 || !$this->_role->hasStoreAccess($store)) {
                        $this->_forward();
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Validate URL Rewrite Management edit action
     *
     * @return bool
     */
    public function validateUrlRewriteEditAction()
    {
        $id = $this->_request->getParam('id');
        if ($id) {
            $object = $this->_objectManager->create('Magento\Core\Model\Url\Rewrite')->load($id);
            if ($object && $object->getId()) {
                if (!$this->_role->hasStoreAccess($object->getStoreId())) {
                    $this->_forward();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate Admin User management actions
     *
     * @return bool
     */
    public function validateAdminUserAction()
    {
        $id = $this->_request->getParam('user_id');
        if ($id) {
            $limited = $this->_collectionsFactory->create()
                ->getUsersOutsideLimitedScope(
                    $this->_role->getIsAll(),
                    $this->_role->getWebsiteIds(),
                    $this->_role->getStoreGroupIds()
                );

            if (in_array($id, $limited)) {
                $this->_forward();
                return false;
            }
        }
        return true;
    }

    /**
     * Validate Admin Role management actions
     *
     * @return bool
     */
    public function validateAdminRoleAction()
    {
        $id = $this->_request->getParam('rid', $this->_request->getParam('role_id'));
        if ($id) {
            $limited = $this->_collectionsFactory->create()
                ->getRolesOutsideLimitedScope(
                    $this->_role->getIsAll(),
                    $this->_role->getWebsiteIds(),
                    $this->_role->getStoreGroupIds()
                );
            if (in_array($id, $limited)) {
                $this->_forward();
                return false;
            }
        }
        return true;
    }

    /**
     * Validate Attribute management actions
     *
     * @return bool
     */
    public function validateCatalogProductAttributeActions()
    {
        if (!$this->_role->getIsAll()) {
            $this->_forward();
            return false;
        }
        return true;
    }

    /**
     * Validate Attribute creation action
     *
     * @return bool
     */
    public function validateCatalogProductAttributeCreateAction()
    {
        if (!$this->_role->getIsAll() && !$this->_request->getParam('attribute_id')) {
            $this->_forward();
            return false;
        }

        return true;
    }

    /**
     * Validate Products in Catalog Product MassDelete Action
     */
    public function catalogProductMassDeleteAction()
    {
        $productIds             = $this->_request->getParam('product');
        $productNotExclusiveIds = array();
        $productExclusiveIds    = array();

        $productsWebsites = $this->_productFactoryRes->create()->getWebsiteIdsByProductIds($productIds);

        foreach ($productsWebsites as $productId => $productWebsiteIds) {
            if (!$this->_role->hasExclusiveAccess($productWebsiteIds)) {
                $productNotExclusiveIds[]  = $productId;
            } else {
                $productExclusiveIds[] = $productId;
            }
        }

        if (!empty($productNotExclusiveIds)) {
            $productNotExclusiveIds = implode(', ', $productNotExclusiveIds);
            $message = __('You need more permissions to delete this item(s): %1.', $productNotExclusiveIds);
            $this->messageManager->addError($message);
        }

        $this->_request->setParam('product', $productExclusiveIds);
    }


    /**
     * Validate Attribute set creation, deletion and saving actions
     *
     * @return bool
     */
    public function validateAttributeSetActions()
    {
        $this->_forward();
        return false;
    }

    /**
     * Validate permission for adding new sub category to specified parent id
     *
     * @param int $categoryId
     *
     * @return bool
     */
    protected function _validateCatalogSubCategoryAddPermission($categoryId)
    {
        $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
        if ($category->getId()) {
            /**
             * viewing for parent category allowed and
             * user has exclusive access to root category
             * so we can allow user to add sub category
             */
            if ($this->_isCategoryAllowed($category)
                && $this->_role->hasExclusiveCategoryAccess($category->getPath())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Block index actions for all GWS limited users.
     *
     * @return bool
     */
    public function blockIndexAction()
    {
        $this->_forward();
        return false;
    }

    /**
     * Validate misc Manage Currency Rates requests
     *
     * @return bool
     */
    public function validateManageCurrencyRates()
    {
        if (in_array($this->_request->getActionName(), array('fetchRates', 'saveRates'))) {
            $this->_forward();
            return false;
        }

        return true;
    }

    /**
     * Validate misc Transactional Emails
     * @return bool
     */
    public function validateTransactionalEmails()
    {
        if (in_array($this->_request->getActionName(), array('delete', 'save', 'new'))) {
            $this->_forward();
            return false;
        }

        return true;
    }

    /**
     * Block save action for all GWS limited users
     *
     * @return bool
     */
    public function blockCustomerGroupSave()
    {
        $this->_forward();
        return false;
    }

    /**
     * Block save and delete action for all GWS limited users
     *
     * @return bool
     */
    public function blockTaxChange()
    {
        $this->_forward();
        return false;
    }

    /**
     * Validate Giftregistry actions : edit, add, share, delete
     *
     * @return bool
     */
    public function validateGiftregistryEntityAction()
    {
        $id = $this->_request->getParam('id', $this->_request->getParam('entity_id'));
        if ($id) {
            $websiteId = $this->_objectManager->create('Magento\GiftRegistry\Model\Entity')
                ->getResource()->getWebsiteIdByEntityId($id);
            if (!in_array($websiteId, $this->_role->getWebsiteIds())) {
                $this->_forward();
                return false;
            }
        } else {
            $this->_forward();
            return false;
        }
        return true;
    }

    /**
     * Validate customer attribute actions
     *
     * @return bool
     */
    public function validateCustomerAttributeActions()
    {
        $actionName = $this->_request->getActionName();
        $attributeId = $this->_request->getParam('attribute_id');
        $websiteId = $this->_request->getParam('website');
        if (in_array($actionName, array('new', 'delete'))
            || (in_array($actionName, array('edit', 'save')) && !$attributeId)
            || ($websiteId && !$this->_role->hasWebsiteAccess($websiteId, true))) {
            $this->_forward();
            return false;
        }
        return true;
    }

    /**
     * Deny certain actions at rule entity in disallowed scopes
     *
     * @return bool
     */
    public function validateRuleEntityAction()
    {
        $request     = $this->_request;
        $denyActions = array('edit', 'new', 'delete', 'save', 'run', 'match');
        $denyChangeDataActions = array('delete', 'save', 'run', 'match');
        $denyCreateDataActions = array('save');
        $actionName  = $request->getActionName();

        // Deny access if role has no allowed website ids and there are considering actions to deny
        if (!$this->_role->getWebsiteIds() && in_array($actionName, $denyActions)) {
            return $this->_forward();
        }

        // Stop further validating if role has any allowed website ids and
        // there are considering any action which is not in deny list
        if (!in_array($actionName, $denyActions)) {
            return true;
        }

        // Stop further validating if there is no an appropriate entity id in request params
        $ruleId = $request->getParam('rule_id', $request->getParam('segment_id', $request->getParam('id', null)));
        if (!$ruleId && !in_array($actionName, $denyCreateDataActions)) {
            return true;
        }

        $controllerName = $request->getControllerName();

        // Determine entity model class name
        switch ($controllerName) {
            case 'promo_catalog':
                $entityModelClassName = 'Magento\CatalogRule\Model\Rule';
                break;
            case 'promo_quote':
                $entityModelClassName = 'Magento\SalesRule\Model\Rule';
                break;
            case 'reminder':
                $entityModelClassName = 'Magento\Reminder\Model\Rule';
                break;
            case 'customersegment':
                $entityModelClassName = 'Magento\CustomerSegment\Model\Segment';
                break;
            default:
                $entityModelClassName = null;
                break;
        }

        if (is_null($entityModelClassName)) {
            return true;
        }

        $entityObject = $this->_objectManager->create($entityModelClassName);
        if (!$entityObject) {
            return true;
        }

        $ruleWebsiteIds = $request->getParam('website_ids', array());
        if ($ruleId) {
            // Deny action if specified rule entity doesn't exist
            $entityObject->load($ruleId);
            if (!$entityObject->getId()) {
                return $this->_forward();
            }
            $ruleWebsiteIds = array_unique(array_merge(
                $ruleWebsiteIds,
                (array)$entityObject->getOrigData('website_ids')
            ));
        }


        // Deny actions what lead to changing data if role has no exclusive access to assigned to rule entity websites
        if (!$this->_role->hasExclusiveAccess($ruleWebsiteIds) && in_array($actionName, $denyChangeDataActions)) {
            return $this->_forward();
        }

        // Deny action if role has no access to assigned to rule entity websites
        if (!$this->_role->hasWebsiteAccess($ruleWebsiteIds)) {
            return $this->_forward();
        }

        return true;
    }

    /**
     * Validate applying catalog rules action
     *
     * @return bool
     */
    public function validatePromoCatalogApplyRules()
    {
        $this->_forward();
        return false;
    }

    /**
     * Promo catalog index action
     *
     * @param \Magento\Backend\App\Action $controller
     * @return \Magento\AdminGws\Model\Controllers
     */
    public function promoCatalogIndexAction($controller)
    {
        $controller->setDirtyRulesNoticeMessage(
            __('There are rules that have been changed but were not applied. Only users with exclusive access can apply rules.')
        );
        return $this;
    }

    /**
     * Block editing of RMA attributes on disallowed websites
     *
     * @param \Magento\Backend\App\Action $controller
     * @return bool|void
     */
    public function validateRmaAttributeEditAction($controller)
    {
        $websiteCode = $this->_request->getParam('website');

        if (!$websiteCode) {
            $allowedWebsitesIds = $this->_role->getWebsiteIds();

            if (!count($allowedWebsitesIds)) {
                $this->_forward();
                return false;
            }

            return $this->_redirect($this->_backendUrl->getUrl(
                    'adminhtml/rma_item_attribute/edit',
                     array('website' => $allowedWebsitesIds[0], '_current' => true)
                )
            );
        }

        try {
            $website = $this->_storeManager->getWebsite($websiteCode);

            if (!$website || !$this->_role->hasWebsiteAccess($website->getId(), true)) {
                $this->_forward();
                return false;
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_forward();
            return false;
        }

        return true;
    }

    /**
     * Block RMA attributes deleting for all GWS enabled users
     *
     * @return bool
     */
    public function validateRmaAttributeDeleteAction()
    {
        $this->_forward();
        return false;
    }

    /**
     * Block deleting of options of attributes for all GWS enabled users
     *
     * @param \Magento\Backend\App\Action $controller
     * @return bool
     */
    public function validateRmaAttributeSaveAction($controller)
    {
        $option = $this->_request->getPost('option');
        if (!empty($option['delete'])) {
            unset($option['delete']);
            $this->_request->setPost('option', $option);
        }

        return $this->validateRmaAttributeEditAction($controller);
    }
}
