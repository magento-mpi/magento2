<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml observer
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
namespace Magento\CatalogPermissions\Model\Adminhtml;

use Magento\App\CacheInterface;
use Magento\AuthorizationInterface;
use Magento\Catalog\Block\Adminhtml\Category\Tabs;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\CatalogPermissions\Helper\Data;
use Magento\CatalogPermissions\Model\Permission;
use Magento\CatalogPermissions\Model\Permission\Index;
use Magento\Event\Observer as EventObserver;
use Magento\Index\Model\Event;
use Magento\Index\Model\Indexer;

class Observer
{
    const FORM_SELECT_ALL_VALUES = -1;

    /**
     * @var array
     */
    protected $_indexQueue = array();

    /**
     * @var array
     */
    protected $_indexProductQueue = array();

    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Catalog permissions data
     *
     * @var Data
     */
    protected $_catalogPermData = null;

    /**
     * @var \Magento\CatalogPermissions\Model\PermissionFactory
     */
    protected $_permissionFactory;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var Indexer
     */
    protected $_indexer;

    /**
     * @var CacheInterface
     */
    protected $_coreCache;

    /**
     * @var ConfigInterface
     */
    protected $_permissionsConfig;

    /**
     * @param ConfigInterface $permissionsConfig
     * @param CacheInterface $coreCache
     * @param CategoryFactory $categoryFactory
     * @param \Magento\CatalogPermissions\Model\PermissionFactory $permissionFactory
     * @param Indexer $indexer
     * @param Data $catalogPermData
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        ConfigInterface $permissionsConfig,
        CacheInterface $coreCache,
        CategoryFactory $categoryFactory,
        \Magento\CatalogPermissions\Model\PermissionFactory $permissionFactory,
        Indexer $indexer,
        Data $catalogPermData,
        AuthorizationInterface $authorization
    ) {
        $this->_permissionsConfig = $permissionsConfig;
        $this->_coreCache = $coreCache;
        $this->_indexer = $indexer;
        $this->_categoryFactory = $categoryFactory;
        $this->_permissionFactory = $permissionFactory;
        $this->_catalogPermData = $catalogPermData;
        $this->_authorization = $authorization;
    }

    /**
     * Save category permissions on category after save event
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function saveCategoryPermissions(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        /* @var $category Category */
        if ($category->hasData('permissions') && is_array($category->getData('permissions'))
            && $this->_authorization
                ->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')
        ) {
            foreach ($category->getData('permissions') as $data) {
                $permission = $this->_permissionFactory->create();
                if (!empty($data['id'])) {
                    $permission->load($data['id']);
                }

                if (!empty($data['_deleted'])) {
                    if ($permission->getId()) {
                        $permission->delete();
                    }
                    continue;
                }

                if ($data['website_id'] == self::FORM_SELECT_ALL_VALUES) {
                    $data['website_id'] = null;
                }

                if ($data['customer_group_id'] == self::FORM_SELECT_ALL_VALUES) {
                    $data['customer_group_id'] = null;
                }

                $permission->addData($data);
                $categoryViewPermission = $permission->getGrantCatalogCategoryView();
                if (Permission::PERMISSION_DENY == $categoryViewPermission) {
                    $permission->setGrantCatalogProductPrice(Permission::PERMISSION_DENY);
                }

                $productPricePermission = $permission->getGrantCatalogProductPrice();
                if (Permission::PERMISSION_DENY == $productPricePermission) {
                    $permission->setGrantCheckoutItems(Permission::PERMISSION_DENY);
                }
                $permission->setCategoryId($category->getId());
                $permission->save();
            }
            $this->_indexQueue[] = $category->getPath();
        }
        return $this;
    }

    /**
     * Reindex category permissions on category move event
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function reindexCategoryPermissionOnMove(EventObserver $observer)
    {
        $category = $this->_categoryFactory->create()
            ->load($observer->getEvent()->getCategoryId());
        $this->_indexQueue[] = $category->getPath();
        return $this;
    }

    /**
     * Reindex permissions in queue on postdipatch
     *
     * @return  $this
     */
    public function reindexPermissions()
    {
        if (!empty($this->_indexQueue)) {
            /** @var $indexer Indexer */
            $indexer = $this->_indexer;
            foreach ($this->_indexQueue as $item) {
                $indexer->logEvent(
                    new \Magento\Object(array('id' => $item)),
                    Index::ENTITY_CATEGORY,
                    Index::EVENT_TYPE_REINDEX_PRODUCTS
                );
            }
            $this->_indexQueue = array();
            $indexer->indexEvents(
                Index::ENTITY_CATEGORY,
                Index::EVENT_TYPE_REINDEX_PRODUCTS
            );
            $this->_coreCache->clean(array(Category::CACHE_TAG));
        }

        if (!empty($this->_indexProductQueue)) {
            /** @var $indexer Indexer */
            $indexer = $this->_indexer;
            foreach ($this->_indexProductQueue as $item) {
                $indexer->logEvent(
                    new \Magento\Object(array('id' => $item)),
                    Index::ENTITY_PRODUCT,
                    Index::EVENT_TYPE_REINDEX_PRODUCTS
                );
            }
            $indexer->indexEvents(
                Index::ENTITY_PRODUCT,
                Index::EVENT_TYPE_REINDEX_PRODUCTS
            );
            $this->_indexProductQueue = array();
        }

        return $this;
    }

    /**
     * Refresh category related cache on catalog permissions config save
     *
     * @return $this
     */
    public function cleanCacheOnConfigChange()
    {
        $this->_coreCache->cleanCache(array(Category::CACHE_TAG));
        $this->_indexer->processEntityAction(
            new \Magento\Object(),
            Index::ENTITY_CONFIG,
            Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Rebuild index for products
     *
     * @return $this
     */
    public function reindexProducts()
    {
        $this->_indexProductQueue[] = null;
        return $this;
    }

    /**
     * Rebuild index
     *
     * @return $this
     */
    public function reindex()
    {
        $this->_indexQueue[] = '1';
        return $this;
    }

    /**
     * Rebuild index after product assigned websites
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function reindexAfterProductAssignedWebsite(EventObserver $observer)
    {
        $productIds = $observer->getEvent()->getProducts();
        $this->_indexProductQueue = array_merge($this->_indexProductQueue, $productIds);
        return $this;
    }


    /**
     * Save product permission index
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function saveProductPermissionIndex(EventObserver $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_indexProductQueue[] = $product->getId();
        return $this;
    }

    /**
     * Add permission tab on category edit page
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function addCategoryPermissionTab(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }
        if (!$this->_authorization->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Tabs */

        //if ($this->_catalogPermissionsData->isAllowedCategory($tabs->getCategory())) {
            $tabs->addTab(
                'permissions',
                'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions'
            );
        //}

        return $this;
    }

    /**
     * Apply categories and products permissions after reindex category products
     *
     * @param EventObserver $observer
     * @return void
     */
    public function applyPermissionsAfterReindex(EventObserver $observer)
    {
        $this->_indexer->getProcessByCode('catalogpermissions')->reindexEverything();
    }
}
