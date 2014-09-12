<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Model\Resource\Event;

/**
 * Catalog Event resource collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Whether category data was added to collection
     *
     * @var bool
     */
    protected $_categoryDataAdded = false;

    /**
     * Whether collection should dispose of the closed events
     *
     * @var bool
     */
    protected $_skipClosed = false;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogEvent\Model\Event', 'Magento\CatalogEvent\Model\Resource\Event');
    }

    /**
     * Redefining of standard field to filter adding, for availability of
     * bit operations for display state
     *
     * @param string $field
     * @param null|string|array $condition
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'display_state') {
            if (is_array($condition) && isset($condition['eq'])) {
                $condition = $condition['eq'];
            }
            if ((int)$condition > 0) {
                $this->getSelect()->where('display_state = 3 OR display_state = ?', (int)$condition);
            }
            return $this;
        }
        if ($field == 'status') {
            $this->getSelect()->where($this->_getConditionSql($this->_getStatusColumnExpr(), $condition));
            return $this;
        }
        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * Add filter for visible events on frontend
     *
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    public function addVisibilityFilter()
    {
        $this->_skipClosed = true;
        $this->addFieldToFilter('status', array('nin' => \Magento\CatalogEvent\Model\Event::STATUS_CLOSED));
        return $this;
    }

    /**
     * Set sort order
     *
     * @param string $field
     * @param string $direction
     * @param boolean $unshift
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    protected function _setOrder($field, $direction, $unshift = false)
    {
        if ($field == 'category_name' && $this->_categoryDataAdded) {
            $field = 'category_position';
        }
        return parent::setOrder($field, $direction, $unshift);
    }

    /**
     * Add category data to collection select (name, position)
     *
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    public function addCategoryData()
    {
        if (!$this->_categoryDataAdded) {
            $this->getSelect()->joinLeft(
                array('category' => $this->getTable('catalog_category_entity')),
                'category.entity_id = main_table.category_id',
                array('category_position' => 'position')
            )->joinLeft(
                array('category_name_attribute' => $this->getTable('eav_attribute')),
                'category_name_attribute.entity_type_id = category.entity_type_id
                    AND category_name_attribute.attribute_code = \'name\'',
                array()
            )->joinLeft(
                array('category_varchar' => $this->getTable('catalog_category_entity_varchar')),
                'category_varchar.entity_id = category.entity_id
                    AND category_varchar.attribute_id = category_name_attribute.attribute_id
                    AND category_varchar.store_id = 0',
                array('category_name' => 'value')
            );
            $this->_map['fields']['category_name'] = 'category_varchar.value';
            $this->_map['fields']['category_position'] = 'category.position';
            $this->_categoryDataAdded = true;
        }
        return $this;
    }

    /**
     * Add sorting by status.
     * first will be open, then upcoming
     *
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    public function addSortByStatus()
    {
        $adapter = $this->getConnection();
        $columnExpr = $adapter->quoteInto(
            $this->_getStatusColumnExpr() . ' = ?',
            \Magento\CatalogEvent\Model\Event::STATUS_OPEN
        );

        $this->getSelect()->order(
            array(
                $adapter->getCheckSql($columnExpr, 0, 1) . ' ASC',
                $adapter->getCheckSql($columnExpr, 'main_table.date_end', 'main_table.date_start') . ' ASC',
                'main_table.sort_order ASC'
            )
        );

        return $this;
    }

    /**
     * Add image data
     *
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    public function addImageData()
    {
        $adapter = $this->getConnection();
        $this->getSelect()->joinLeft(
            array('event_image' => $this->getTable('magento_catalogevent_event_image')),
            implode(
                ' AND ',
                array(
                    'event_image.event_id = main_table.event_id',
                    $adapter->quoteInto('event_image.store_id = ?', $this->_storeManager->getStore()->getId())
                )
            ),
            array(
                'image' => $adapter->getCheckSql(
                    'event_image.image IS NULL',
                    'event_image_default.image',
                    'event_image.image'
                )
            )
        )->joinLeft(
            array('event_image_default' => $this->getTable('magento_catalogevent_event_image')),
            'event_image_default.event_id = main_table.event_id AND event_image_default.store_id = 0',
            array()
        );

        return $this;
    }

    /**
     * Limit collection by specified category paths
     *
     * @param array $allowedPaths
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    public function capByCategoryPaths($allowedPaths)
    {
        $this->addCategoryData();
        $paths = array();
        foreach ($allowedPaths as $path) {
            $paths[] = $this->getConnection()->quoteInto('category.path = ?', $path);
            $paths[] = $this->getConnection()->quoteInto('category.path LIKE ?', $path . '/%');
        }
        if ($paths) {
            $this->getSelect()->where(implode(' OR ', $paths));
        }
        return $this;
    }

    /**
     * Override _afterLoad() implementation
     *
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    protected function _afterLoad()
    {
        $events = parent::_afterLoad();
        foreach ($events->_items as $event) {
            if ($this->_skipClosed && $event->getStatus() == \Magento\CatalogEvent\Model\Event::STATUS_CLOSED) {
                $this->removeItemByKey($event->getId());
            }
        }
        return $this;
    }

    /**
     * Reset collection
     *
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    protected function _reset()
    {
        $this->_skipClosed = false;
        return parent::_reset();
    }

    /**
     * Retrieve DB Expression for status column
     *
     * @return \Zend_Db_Expr
     */
    protected function _getStatusColumnExpr()
    {
        $adapter = $this->getConnection();
        $timeNow = $this->dateTime->formatDate(true);
        $dateStart1 = $adapter->quoteInto('date_start <= ?', $timeNow);
        $dateEnd1 = $adapter->quoteInto('date_end >= ?', $timeNow);
        $dateStart2 = $adapter->quoteInto('date_start > ?', $timeNow);
        $dateEnd2 = $adapter->quoteInto('date_end > ?', $timeNow);

        return $adapter->getCaseSql(
            '',
            array(
                "({$dateStart1} AND {$dateEnd1})" => $adapter->quote(\Magento\CatalogEvent\Model\Event::STATUS_OPEN),
                "({$dateStart2} AND {$dateEnd2})" => $adapter->quote(
                    \Magento\CatalogEvent\Model\Event::STATUS_UPCOMING
                )
            ),
            $adapter->quote(\Magento\CatalogEvent\Model\Event::STATUS_CLOSED)
        );
    }

    /**
     * Add status column based on dates
     *
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->columns(array('status' => $this->_getStatusColumnExpr()));
        return $this;
    }
}
