<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Category resource collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Category;

class Collection extends \Magento\Catalog\Model\Resource\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix              = 'catalog_category_collection';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject              = 'category_collection';

    /**
     * Name of product table
     *
     * @var string
     */
    protected $_productTable;

    /**
     * Store id, that we should count products on
     *
     * @var int
     */
    protected $_productStoreId;

    /**
     * Name of product website table
     *
     * @var string
     */
    protected $_productWebsiteTable;

    /**
     * Load with product count flag
     *
     * @var boolean
     */
    protected $_loadWithProductCount     = false;

    /**
     * Init collection and determine table names
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\Category', 'Magento\Catalog\Model\Resource\Category');

        $this->_productWebsiteTable = $this->getTable('catalog_product_website');
        $this->_productTable        = $this->getTable('catalog_category_product');
    }

    /**
     * Add Id filter
     *
     * @param array $categoryIds
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function addIdFilter($categoryIds)
    {
        if (is_array($categoryIds)) {
            if (empty($categoryIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $categoryIds);
            }
        } elseif (is_numeric($categoryIds)) {
            $condition = $categoryIds;
        } elseif (is_string($categoryIds)) {
            $ids = explode(',', $categoryIds);
            if (empty($ids)) {
                $condition = $categoryIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Set flag for loading product count
     *
     * @param boolean $flag
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function setLoadProductCount($flag)
    {
        $this->_loadWithProductCount = $flag;
        return $this;
    }

    /**
     * Before collection load
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected function _beforeLoad()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_before',
                            array($this->_eventObject => $this));
        return parent::_beforeLoad();
    }

    /**
     * After collection load
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected function _afterLoad()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after',
                            array($this->_eventObject => $this));

        return parent::_afterLoad();
    }

    /**
     * Set id of the store that we should count products on
     *
     * @param int $storeId
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function setProductStoreId($storeId)
    {
        $this->_productStoreId = $storeId;
        return $this;
    }

    /**
     * Get id of the store that we should count products on
     *
     * @return int
     */
    public function getProductStoreId()
    {
        if (is_null($this->_productStoreId)) {
            $this->_productStoreId = \Magento\Catalog\Model\AbstractModel::DEFAULT_STORE_ID;
        }
        return $this->_productStoreId;
    }

    /**
     * Load collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        if ($this->_loadWithProductCount) {
            $this->addAttributeToSelect('all_children');
            $this->addAttributeToSelect('is_anchor');
        }

        parent::load($printQuery, $logQuery);

        if ($this->_loadWithProductCount) {
            $this->_loadProductCount();
        }

        return $this;
    }

    /**
     * Load categories product count
     *
     */
    protected function _loadProductCount()
    {
        $this->loadProductCount($this->_items, true, true);
    }

    /**
     * Load product count for specified items
     *
     * @param array $items
     * @param boolean $countRegular get product count for regular (non-anchor) categories
     * @param boolean $countAnchor get product count for anchor categories
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function loadProductCount($items, $countRegular = true, $countAnchor = true)
    {
        $anchor     = array();
        $regular    = array();
        $websiteId  = $this->_storeManager->getStore($this->getProductStoreId())->getWebsiteId();

        foreach ($items as $item) {
            if ($item->getIsAnchor()) {
                $anchor[$item->getId()] = $item;
            } else {
                $regular[$item->getId()] = $item;
            }
        }

        if ($countRegular) {
            // Retrieve regular categories product counts
            $regularIds = array_keys($regular);
            if (!empty($regularIds)) {
                $select = $this->_conn->select();
                $select->from(
                        array('main_table' => $this->_productTable),
                        array('category_id', new \Zend_Db_Expr('COUNT(main_table.product_id)'))
                    )
                    ->where($this->_conn->quoteInto('main_table.category_id IN(?)', $regularIds))
                    ->group('main_table.category_id');
                if ($websiteId) {
                    $select->join(
                        array('w' => $this->_productWebsiteTable),
                        'main_table.product_id = w.product_id', array()
                    )
                    ->where('w.website_id = ?', $websiteId);
                }
                $counts = $this->_conn->fetchPairs($select);
                foreach ($regular as $item) {
                    if (isset($counts[$item->getId()])) {
                        $item->setProductCount($counts[$item->getId()]);
                    } else {
                        $item->setProductCount(0);
                    }
                }
            }
        }

        if ($countAnchor) {
            // Retrieve Anchor categories product counts
            foreach ($anchor as $item) {
                if ($allChildren = $item->getAllChildren()) {
                    $bind = array(
                        'entity_id' => $item->getId(),
                        'c_path'    => $item->getPath() . '/%'
                    );
                    $select = $this->_conn->select();
                    $select->from(
                            array('main_table' => $this->_productTable),
                            new \Zend_Db_Expr('COUNT(DISTINCT main_table.product_id)')
                        )
                        ->joinInner(
                            array('e' => $this->getTable('catalog_category_entity')),
                            'main_table.category_id=e.entity_id',
                            array()
                        )
                        ->where('e.entity_id = :entity_id')
                        ->orWhere('e.path LIKE :c_path');
                    if ($websiteId) {
                        $select->join(
                            array('w' => $this->_productWebsiteTable),
                            'main_table.product_id = w.product_id', array()
                        )
                        ->where('w.website_id = ?', $websiteId);
                    }
                    $item->setProductCount((int) $this->_conn->fetchOne($select, $bind));
                } else {
                    $item->setProductCount(0);
                }
            }
        }
        return $this;
    }

    /**
     * Add category path filter
     *
     * @param string $regexp
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function addPathFilter($regexp)
    {
        $this->addFieldToFilter('path', array('regexp' => $regexp));
        return $this;
    }

    /**
     * Joins url rewrite rules to collection
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function joinUrlRewrite()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $this->joinTable(
            'core_url_rewrite',
            'category_id=entity_id',
            array('request_path'),
            "{{table}}.is_system=1"
                . " AND {{table}}.product_id IS NULL"
                . " AND {{table}}.store_id='{$storeId}'"
                . " AND id_path LIKE 'category/%'",
            'left'
        );
        return $this;
    }

    /**
     * Add active category filter
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function addIsActiveFilter()
    {
        $this->addAttributeToFilter('is_active', 1);
        $this->_eventManager->dispatch($this->_eventPrefix . '_add_is_active_filter',
                            array($this->_eventObject => $this));
        return $this;
    }

    /**
     * Add name attribute to result
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function addNameToResult()
    {
        $this->addAttributeToSelect('name');
        return $this;
    }

    /**
     * Add url rewrite rules to collection
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function addUrlRewriteToResult()
    {
        $this->joinUrlRewrite();
        return $this;
    }

    /**
     * Add category path filter
     *
     * @param array|string $paths
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $write  = $this->getResource()->getWriteConnection();
        $cond   = array();
        foreach ($paths as $path) {
            $cond[] = $write->quoteInto('e.path LIKE ?', "$path%");
        }
        if ($cond) {
            $this->getSelect()->where(join(' OR ', $cond));
        }
        return $this;
    }

    /**
     * Add category level filter
     *
     * @param int|string $level
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function addLevelFilter($level)
    {
        $this->addFieldToFilter('level', array('lteq' => $level));
        return $this;
    }

    /**
     * Add root category filter
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function addRootLevelFilter()
    {
        $this->addFieldToFilter('path', array('neq' => '1'));
        $this->addLevelFilter(1);
        return $this;
    }

    /**
     * Add order field
     *
     * @param string $field
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function addOrderField($field)
    {
        $this->setOrder($field, self::SORT_ORDER_ASC);
        return $this;
    }
}
