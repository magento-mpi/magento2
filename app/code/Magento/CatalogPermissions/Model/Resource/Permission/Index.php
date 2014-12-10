<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Resource\Permission;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Resource\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\Resource\Category\Flat\Collection as FlatCollection;
use Magento\Catalog\Model\Resource\Product\Collection as ProductCollection;
use Magento\CatalogPermissions\Helper\Data as Helper;
use Magento\CatalogPermissions\Model\Permission;
use Magento\Store\Model\Store;
use Magento\Framework\StoreManagerInterface;
use Magento\Eav\Model\Entity\Attribute;

class Index extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Catalog permissions data
     *
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param Helper $helper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Framework\App\Resource $resource, Helper $helper, StoreManagerInterface $storeManager)
    {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        parent::__construct($resource);
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_catalogpermissions_index', 'category_id');
    }

    /**
     * Return product index table
     *
     * @return string
     */
    protected function getProductTable()
    {
        return $this->getMainTable() . \Magento\CatalogPermissions\Model\Indexer\AbstractAction::PRODUCT_SUFFIX;
    }

    /**
     * Retrieve permission index for category or categories with specified customer group and website id
     *
     * @param int|int[] $categoryId
     * @param int $customerGroupId
     * @param int $websiteId
     * @return array
     */
    public function getIndexForCategory($categoryId, $customerGroupId = null, $websiteId = null)
    {
        $adapter = $this->_getReadAdapter();
        if (!is_array($categoryId)) {
            $categoryId = array($categoryId);
        }

        $select = $adapter->select()->from($this->getMainTable())->where('category_id IN (?)', $categoryId);
        if (!is_null($customerGroupId)) {
            $select->where('customer_group_id = ?', $customerGroupId);
        }
        if (!is_null($websiteId)) {
            $select->where('website_id = ?', $websiteId);
        }

        return !is_null(
            $customerGroupId
        ) && !is_null(
            $websiteId
        ) ? $adapter->fetchAssoc(
            $select
        ) : $adapter->fetchAll(
            $select
        );
    }

    /**
     * Retrieve restricted category ids for customer group and website
     *
     * @param int $customerGroupId
     * @param int $websiteId
     * @return array
     */
    public function getRestrictedCategoryIds($customerGroupId, $websiteId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable(),
            'category_id'
        )->where(
            'grant_catalog_category_view = :grant_catalog_category_view'
        );
        $bind = array();
        if ($customerGroupId) {
            $select->where('customer_group_id = :customer_group_id');
            $bind[':customer_group_id'] = $customerGroupId;
        }
        if ($websiteId) {
            $select->where('website_id = :website_id');
            $bind[':website_id'] = $websiteId;
        }
        if (!$this->helper->isAllowedCategoryView()) {
            $bind[':grant_catalog_category_view'] = Permission::PERMISSION_ALLOW;
        } else {
            $bind[':grant_catalog_category_view'] = Permission::PERMISSION_DENY;
        }

        $restrictedCatIds = $adapter->fetchCol($select, $bind);

        $select = $adapter->select()->from($this->getTable('catalog_category_entity'), 'entity_id');

        if (!empty($restrictedCatIds) && !$this->helper->isAllowedCategoryView()) {
            $select->where('entity_id NOT IN(?)', $restrictedCatIds);
        } elseif (!empty($restrictedCatIds) && $this->helper->isAllowedCategoryView()) {
            $select->where('entity_id IN(?)', $restrictedCatIds);
        } elseif ($this->helper->isAllowedCategoryView()) {
            // category view allowed for all
            $select->where('1 = 0');
        }

        return $adapter->fetchCol($select);
    }

    /**
     * Add index to category collection
     *
     * @param CategoryCollection|FlatCollection $collection
     * @param int $customerGroupId
     * @param int $websiteId
     * @return $this
     */
    public function addIndexToCategoryCollection(CategoryCollection $collection, $customerGroupId, $websiteId)
    {
        $adapter = $this->_getReadAdapter();
        if ($collection instanceof FlatCollection) {
            $tableAlias = 'main_table';
        } else {
            $tableAlias = 'e';
        }

        $collection->getSelect()->joinLeft(
            array('perm' => $this->getMainTable()),
            'perm.category_id = ' . $tableAlias . '.entity_id' . ' AND ' . $adapter->quoteInto(
                'perm.website_id = ?',
                $websiteId
            ) . ' AND ' . $adapter->quoteInto(
                'perm.customer_group_id = ?',
                $customerGroupId
            ),
            array()
        );

        if (!$this->helper->isAllowedCategoryView()) {
            $collection->getSelect()->where('perm.grant_catalog_category_view = ?', Permission::PERMISSION_ALLOW);
        } else {
            $collection->getSelect()->where(
                'perm.grant_catalog_category_view != ?' . ' OR perm.grant_catalog_category_view IS NULL',
                Permission::PERMISSION_DENY
            );
        }

        return $this;
    }

    /**
     * Add index select in product collection
     *
     * @param ProductCollection $collection
     * @param int $customerGroupId
     * @return $this
     */
    public function addIndexToProductCollection(ProductCollection $collection, $customerGroupId)
    {
        $adapter = $this->_getReadAdapter();

        $fromPart = $collection->getSelect()->getPart(\Zend_Db_Select::FROM);

        $categoryId = isset(
            $collection->getLimitationFilters()['category_id']
        ) ? $collection->getLimitationFilters()['category_id'] : null;

        $conditions = array($adapter->quoteInto('perm.customer_group_id = ?', $customerGroupId));

        if (!$categoryId || $categoryId == $this->storeManager->getStore(
            $collection->getStoreId()
        )->getRootCategoryId()
        ) {
            $conditions[] = 'perm.product_id = cat_index.product_id';
            $conditions[] = $adapter->quoteInto('perm.store_id = ?', $collection->getStoreId());
            $joinConditions = join(' AND ', $conditions);
            $tableName = $this->getProductTable();

            if (!isset($fromPart['perm'])) {
                $collection->getSelect()->joinLeft(
                    array('perm' => $tableName),
                    $joinConditions,
                    array('grant_catalog_category_view', 'grant_catalog_product_price', 'grant_checkout_items')
                );
            }
        } else {
            $conditions[] = 'perm.category_id = cat_index.category_id';
            $conditions[] = $adapter->quoteInto(
                'perm.website_id = ?',
                $this->storeManager->getStore($collection->getStoreId())->getWebsiteId()
            );
            $joinConditions = join(' AND ', $conditions);
            $tableName = $this->getMainTable();

            if (!isset($fromPart['perm'])) {
                $collection->getSelect()->joinLeft(
                    array('perm' => $tableName),
                    $joinConditions,
                    array('grant_catalog_category_view', 'grant_catalog_product_price', 'grant_checkout_items')
                );
            }
        }

        if (isset($fromPart['perm'])) {
            $fromPart['perm']['tableName'] = $tableName;
            $fromPart['perm']['joinCondition'] = $joinConditions;
            $collection->getSelect()->setPart(\Zend_Db_Select::FROM, $fromPart);
            return $this;
        }

        if (!$this->helper->isAllowedCategoryView()) {
            $collection->getSelect()->where('perm.grant_catalog_category_view = ?', Permission::PERMISSION_ALLOW);
        } else {
            $collection->getSelect()->where(
                'perm.grant_catalog_category_view != ?' . ' OR perm.grant_catalog_category_view IS NULL',
                Permission::PERMISSION_DENY
            );
        }

        $this->addLinkLimitation($collection);

        return $this;
    }

    /**
     * Add link limitations to product collection
     *
     * @param ProductCollection $collection
     * @return $this
     */
    protected function addLinkLimitation($collection)
    {
        if (method_exists($collection, 'getLinkModel') || $collection->getFlag('is_link_collection')) {
            $collection->getSelect()->where(
                'perm.grant_catalog_product_price != ?' . ' OR perm.grant_catalog_product_price IS NULL',
                Permission::PERMISSION_DENY
            )->where(
                'perm.grant_checkout_items != ?' . ' OR perm.grant_checkout_items IS NULL',
                Permission::PERMISSION_DENY
            );
        }
        return $this;
    }

    /**
     * Add permission index to product model
     *
     * @param Product $product
     * @param int $customerGroupId
     * @return $this
     */
    public function addIndexToProduct($product, $customerGroupId)
    {
        $adapter = $this->_getReadAdapter();

        if ($product->getCategory()) {
            $select = $adapter->select()->from(
                array('perm' => $this->getMainTable()),
                array('grant_catalog_category_view', 'grant_catalog_product_price', 'grant_checkout_items')
            )->where(
                'category_id = ?',
                $product->getCategory()->getId()
            )->where(
                'customer_group_id = ?',
                $customerGroupId
            )->where(
                'website_id = ?',
                $this->storeManager->getStore($product->getStoreId())->getWebsiteId()
            );
        } else {
            $select = $adapter->select()->from(
                array('perm' => $this->getProductTable()),
                array('grant_catalog_category_view', 'grant_catalog_product_price', 'grant_checkout_items')
            )->where(
                'product_id = ?',
                $product->getId()
            )->where(
                'customer_group_id = ?',
                $customerGroupId
            )->where(
                'store_id = ?',
                $product->getStoreId()
            );
        }

        $permission = $adapter->fetchRow($select);
        if ($permission) {
            $product->addData($permission);
        }

        return $this;
    }

    /**
     * Get permission index for products
     *
     * @param int|int[] $productId
     * @param int $customerGroupId
     * @param int $storeId
     * @return array
     */
    public function getIndexForProduct($productId, $customerGroupId, $storeId)
    {
        if (!is_array($productId)) {
            $productId = array($productId);
        }

        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            array('perm' => $this->getProductTable()),
            array('product_id', 'grant_catalog_category_view', 'grant_catalog_product_price', 'grant_checkout_items')
        )->where(
            'product_id IN (?)',
            $productId
        )->where(
            'customer_group_id = ?',
            $customerGroupId
        )->where(
            'store_id = ?',
            $storeId
        );

        return $adapter->fetchAssoc($select);
    }
}
