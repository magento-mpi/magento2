<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Category\Flat;

use Magento\CatalogUrlRewrite\Model\Category\UrlGenerator;
use Magento\Core\Model\EntityFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Model\Resource\Db\AbstractDb;
use Magento\Framework\Logger;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Catalog category flat collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'catalog_category_collection';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = 'category_collection';

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Store id of application
     *
     * @var integer
     */
    protected $_storeId;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param \Zend_Db_Adapter_Abstract $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        $connection = null,
        AbstractDb $resource = null
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\Category', 'Magento\Catalog\Model\Resource\Category\Flat');
    }

    /**
     * @return $this
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
     * @param array|int|string $categoryIds
     * @return $this
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
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_before', array($this->_eventObject => $this));
        return parent::_beforeLoad();
    }

    /**
     * After collection load
     *
     * @return $this
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
     * @return $this
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
            return $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Add filter by path to collection
     *
     * @param string $parent
     * @return $this
     */
    public function addParentPathFilter($parent)
    {
        $this->addFieldToFilter('path', array('like' => "{$parent}/%"));
        return $this;
    }

    /**
     * Add store filter
     *
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', 1);
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_add_is_active_filter',
            array($this->_eventObject => $this)
        );
        return $this;
    }

    /**
     * Add name field to result
     *
     * @return $this
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
     * @return $this
     */
    public function addAttributeToSelect($attribute = '*')
    {
        if ($attribute == '*') {
            // Save previous selected columns
            $columns = $this->getSelect()->getPart(\Zend_Db_Select::COLUMNS);
            $this->getSelect()->reset(\Zend_Db_Select::COLUMNS);
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
     * @return \Magento\Catalog\Model\Resource\Category\Flat
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
     * @return $this
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
     * @param null|string|array $condition
     * @return $this
     */
    public function addAttributeToFilter($attribute, $condition = null)
    {
        if (!is_string($attribute) || $condition === null) {
            return $this;
        }

        return $this->addFieldToFilter($attribute, $condition);
    }

    /**
     * @return $this
     */
    public function addUrlRewriteToResult()
    {
        $this->getSelect()
            ->joinLeft(
                ['url_rewrite' => $this->getTable('url_rewrite')],
                'url_rewrite.entity_id = main_table.entity_id',
                ['request_path']
            )
            ->where('url_rewrite.is_autogenerated = 1')
            ->where('url_rewrite.store_id = ' . (int)$this->_storeManager->getStore()->getId())
            ->where('url_rewrite.entity_type = ?', UrlGenerator::ENTITY_TYPE_CATEGORY);
    }

    /**
     * @param string|array $paths
     * @return $this
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
                $select->orWhere('main_table.path LIKE ?', "{$path}%");
            } else {
                $select->where('main_table.path LIKE ?', "{$path}%");
                $orWhere = true;
            }
        }
        return $this;
    }

    /**
     * @param string $level
     * @return $this
     */
    public function addLevelFilter($level)
    {
        $this->getSelect()->where('main_table.level <= ?', $level);
        return $this;
    }

    /**
     * @param string $field
     * @return $this
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
     * @return $this
     */
    public function setPage($pageNum, $pageSize)
    {
        $this->setCurPage($pageNum)->setPageSize($pageSize);
        return $this;
    }
}
