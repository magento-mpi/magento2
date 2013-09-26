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
class Magento_CatalogPermissions_Model_Adminhtml_Observer
{
    const FORM_SELECT_ALL_VALUES = -1;

    protected $_indexQueue = array();
    protected $_indexProductQueue = array();

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Catalog permissions data
     *
     * @var Magento_CatalogPermissions_Helper_Data
     */
    protected $_catalogPermData = null;

    /**
     * @var Magento_CatalogPermissions_Model_PermissionFactory
     */
    protected $_permissionFactory;

    /**
     * @var Magento_Catalog_Model_CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexer;

    /**
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_coreCache;

    /**
     * @param Magento_Core_Model_CacheInterface $coreCache
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param Magento_CatalogPermissions_Model_PermissionFactory $permissionFactory
     * @param Magento_Index_Model_Indexer $indexer
     * @param Magento_CatalogPermissions_Helper_Data $catalogPermData
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(
        Magento_Core_Model_CacheInterface $coreCache,
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        Magento_CatalogPermissions_Model_PermissionFactory $permissionFactory,
        Magento_Index_Model_Indexer $indexer,
        Magento_CatalogPermissions_Helper_Data $catalogPermData,
        Magento_AuthorizationInterface $authorization
    ) {
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function saveCategoryPermissions(Magento_Event_Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        /* @var $category Magento_Catalog_Model_Category */
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
                if (Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY == $categoryViewPermission) {
                    $permission->setGrantCatalogProductPrice(
                        Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY
                    );
                }

                $productPricePermission = $permission->getGrantCatalogProductPrice();
                if (Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY == $productPricePermission) {
                    $permission->setGrantCheckoutItems(Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY);
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexCategoryPermissionOnMove(Magento_Event_Observer $observer)
    {
        $category = $this->_categoryFactory->create()
            ->load($observer->getEvent()->getCategoryId());
        $this->_indexQueue[] = $category->getPath();
        return $this;
    }

    /**
     * Reindex permissions in queue on postdipatch
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexPermissions()
    {
        if (!empty($this->_indexQueue)) {
            /** @var $indexer Magento_Index_Model_Indexer */
            $indexer = $this->_indexer;
            foreach ($this->_indexQueue as $item) {
                $indexer->logEvent(
                    new Magento_Object(array('id' => $item)),
                    Magento_CatalogPermissions_Model_Permission_Index::ENTITY_CATEGORY,
                    Magento_CatalogPermissions_Model_Permission_Index::EVENT_TYPE_REINDEX_PRODUCTS
                );
            }
            $this->_indexQueue = array();
            $indexer->indexEvents(
                Magento_CatalogPermissions_Model_Permission_Index::ENTITY_CATEGORY,
                Magento_CatalogPermissions_Model_Permission_Index::EVENT_TYPE_REINDEX_PRODUCTS
            );
            $this->_coreCache->clean(array(Magento_Catalog_Model_Category::CACHE_TAG));
        }

        if (!empty($this->_indexProductQueue)) {
            /** @var $indexer Magento_Index_Model_Indexer */
            $indexer = $this->_indexer;
            foreach ($this->_indexProductQueue as $item) {
                $indexer->logEvent(
                    new Magento_Object(array('id' => $item)),
                    Magento_CatalogPermissions_Model_Permission_Index::ENTITY_PRODUCT,
                    Magento_CatalogPermissions_Model_Permission_Index::EVENT_TYPE_REINDEX_PRODUCTS
                );
            }
            $indexer->indexEvents(
                Magento_CatalogPermissions_Model_Permission_Index::ENTITY_PRODUCT,
                Magento_CatalogPermissions_Model_Permission_Index::EVENT_TYPE_REINDEX_PRODUCTS
            );
            $this->_indexProductQueue = array();
        }

        return $this;
    }

    /**
     * Refresh category related cache on catalog permissions config save
     *
     * @return Magento_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function cleanCacheOnConfigChange()
    {
        $this->_coreCache->cleanCache(array(Magento_Catalog_Model_Category::CACHE_TAG));
        $this->_indexer->processEntityAction(
            new Magento_Object(),
            Magento_CatalogPermissions_Model_Permission_Index::ENTITY_CONFIG,
            Magento_Index_Model_Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Rebuild index for products
     *
     * @return  Magento_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexProducts()
    {
        $this->_indexProductQueue[] = null;
        return $this;
    }

    /**
     * Rebuild index
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindex()
    {
        $this->_indexQueue[] = '1';
        return $this;
    }

    /**
     * Rebuild index after product assigned websites
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexAfterProductAssignedWebsite(Magento_Event_Observer $observer)
    {
        $productIds = $observer->getEvent()->getProducts();
        $this->_indexProductQueue = array_merge($this->_indexProductQueue, $productIds);
        return $this;
    }


    /**
     * Save product permission index
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function saveProductPermissionIndex(Magento_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_indexProductQueue[] = $product->getId();
        return $this;
    }

    /**
     * Add permission tab on category edit page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function addCategoryPermissionTab(Magento_Event_Observer $observer)
    {
        if (!$this->_catalogPermData->isEnabled()) {
            return $this;
        }
        if (!$this->_authorization->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Magento_Adminhtml_Block_Catalog_Category_Tabs */

        //if ($this->_catalogPermissionsData->isAllowedCategory($tabs->getCategory())) {
            $tabs->addTab(
                'permissions',
                'Magento_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions'
            );
        //}

        return $this;
    }

    /**
     * Apply categories and products permissions after reindex category products
     *
     * @param Magento_Event_Observer $observer
     */
    public function applyPermissionsAfterReindex(Magento_Event_Observer $observer)
    {
        $this->_indexer->getProcessByCode('catalogpermissions')->reindexEverything();
    }
}
