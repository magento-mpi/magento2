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
 * Catalog category flat collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Category_Flat_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'catalog_category_collection';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject    = 'category_collection';

    /**
     * Store id of application
     *
     * @var integer
     */
    protected $_storeId        = null;

    /**
     *  Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Category', 'Magento_Catalog_Model_Resource_Category_Flat');
    }

    /**
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(
            array('main_table' => $this->getResource()->getMainStoreTable($this->getStoreId())),
            array('entity_id', 'level', 'path', 'position', 'is_active', 'is_anchor')
        );
        return $this;
    }

    /**
     * Add filter by entity id(s).
     *
     * @param mixed $categoryIds
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
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
     * Before collection load
     *
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    protected function _beforeLoad()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_before', array($this->_eventObject => $this));
        return parent::_beforeLoad();
    }

    /**
     * After collection load
     *
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    protected function _afterLoad()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', array($this->_eventObject => $this));
        return parent::_afterLoad();
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id.
     * If store id is not set yet, return store of application
     *
     * @return integer
     */
    public function getStoreId()
    {
        if (null === $this->_storeId) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Add filter by path to collection
     *
     * @param string $parent
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addParentPathFilter($parent)
    {
        $this->addFieldToFilter('path', array('like' => "{$parent}/%"));
        return $this;
    }

    /**
     * Add store filter
     *
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addStoreFilter()
    {
        $this->addFieldToFilter('main_table.store_id', $this->getStoreId());
        return $this;
    }

    /**
     * Set field to sort by
     *
     * @param string $sorted
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addSortedField($sorted)
    {
        if (is_string($sorted)) {
            $this->addOrder($sorted, self::SORT_ORDER_ASC);
        } else {
            $this->addOrder('name', self::SORT_ORDER_ASC);
        }
        return $this;
    }

    /**
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', 1);
        $this->_eventManager->dispatch($this->_eventPrefix . '_add_is_active_filter',
                            array($this->_eventObject => $this));
        return $this;
    }

    /**
     * Add name field to result
     *
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addNameToResult()
    {
        $this->addAttributeToSelect('name');
        return $this;
    }

    /**
     * Add attribute to select
     *
     * @param array|string $attribute
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addAttributeToSelect($attribute = '*')
    {
        if ($attribute == '*') {
            // Save previous selected columns
            $columns = $this->getSelect()->getPart(Zend_Db_Select::COLUMNS);
            $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
            foreach ($columns as $column) {
                if ($column[0] == 'main_table') {
                    // If column selected from main table,
                    // no need to select it again
                    continue;
                }

                // Joined columns
                if ($column[2] !== null) {
                    $expression = array($column[2] => $column[1]);
                } else {
                    $expression = $column[2];
                }
                $this->getSelect()->columns($expression, $column[0]);
            }

            $this->getSelect()->columns('*', 'main_table');
            return $this;
        }

        if (!is_array($attribute)) {
            $attribute = array($attribute);
        }

        $this->getSelect()->columns($attribute, 'main_table');
        return $this;
    }

    /**
     * Retrieve resource instance
     *
     * @return Magento_Catalog_Model_Resource_Category_Flat
     */
    public function getResource()
    {
        return parent::getResource();
    }

    /**
     * Add attribute to sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if (!is_string($attribute)) {
            return $this;
        }
        $this->setOrder($attribute, $dir);
        return $this;
    }

    /**
     * Emulate simple add attribute filter to collection
     *
     * @param string $attribute
     * @param mixed $condition
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addAttributeToFilter($attribute, $condition = null)
    {
        if (!is_string($attribute) || $condition === null) {
            return $this;
        }

        return $this->addFieldToFilter($attribute, $condition);
    }

    /**
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addUrlRewriteToResult()
    {
        $storeId = Mage::app()->getStore()->getId();
        $this->getSelect()->joinLeft(
            array('url_rewrite' => $this->getTable('core_url_rewrite')),
            'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 '
            . 'AND url_rewrite.product_id IS NULL'
            . ' AND ' . $this->getConnection()->quoteInto('url_rewrite.store_id=?', $storeId)
            . ' AND ' . $this->getConnection()->quoteInto('url_rewrite.id_path LIKE ?', 'category/%'),
            array('request_path')
        );
        return $this;
    }

    /**
     * @param string|array $paths
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $select = $this->getSelect();
        $orWhere = false;
        foreach ($paths as $path) {
            if ($orWhere) {
                $select->orWhere('main_table.path LIKE ?', "$path%");
            } else {
                $select->where('main_table.path LIKE ?', "$path%");
                $orWhere = true;
            }
        }
        return $this;
    }

    /**
     * @param string $level
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addLevelFilter($level)
    {
        $this->getSelect()->where('main_table.level <= ?', $level);
        return $this;
    }

    /**
     * @param string $field
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function addOrderField($field)
    {
        $this->setOrder('main_table.' . $field, self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Set collection page start and records to show
     *
     * @param integer $pageNum
     * @param integer $pageSize
     * @return Magento_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function setPage($pageNum, $pageSize)
    {
        $this->setCurPage($pageNum)
            ->setPageSize($pageSize);
        return $this;
    }
}
