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
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(Magento_AuthorizationInterface $authorization)
    {
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
        if (!Mage::helper('Magento_CatalogPermissions_Helper_Data')->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        /* @var $category Magento_Catalog_Model_Category */
        if ($category->hasData('permissions') && is_array($category->getData('permissions'))
            && $this->_authorization
                ->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')
        ) {
            foreach ($category->getData('permissions') as $data) {
                $permission = Mage::getModel('Magento_CatalogPermissions_Model_Permission');
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
        $category = Mage::getModel('Magento_Catalog_Model_Category')
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
            $indexer = Mage::getSingleton('Magento_Index_Model_Indexer');
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
            Mage::app()->cleanCache(array(Magento_Catalog_Model_Category::CACHE_TAG));
        }

        if (!empty($this->_indexProductQueue)) {
            /** @var $indexer Magento_Index_Model_Indexer */
            $indexer = Mage::getSingleton('Magento_Index_Model_Indexer');
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
        Mage::app()->cleanCache(array(Magento_Catalog_Model_Category::CACHE_TAG));
        Mage::getSingleton('Magento_Index_Model_Indexer')->processEntityAction(
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
        if (!Mage::helper('Magento_CatalogPermissions_Helper_Data')->isEnabled()) {
            return $this;
        }
        if (!$this->_authorization->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Magento_Adminhtml_Block_Catalog_Category_Tabs */

        //if (Mage::helper('Magento_CatalogPermissions_Helper_Data')->isAllowedCategory($tabs->getCategory())) {
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
        Mage::getSingleton('Magento_Index_Model_Indexer')->getProcessByCode('catalogpermissions')->reindexEverything();
    }
}
