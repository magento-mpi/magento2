<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Compare\Item;

/**
 * Catalog Product Compare Items Resource Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Collection extends \Magento\Catalog\Model\Resource\Product\Collection
{
    /**
     * Customer Filter
     *
     * @var int
     */
    protected $_customerId = 0;

    /**
     * Visitor Filter
     *
     * @var int
     */
    protected $_visitorId = 0;

    /**
     * Comparable attributes cache
     *
     * @var array
     */
    protected $_comparableAttributes;

    /**
     * Catalog product compare
     *
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $_catalogProductCompare = null;

    /**
     * Catalog product compare item
     *
     * @var \Magento\Catalog\Model\Resource\Product\Compare\Item
     */
    protected $_catalogProductCompareItem;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\Resource\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Catalog\Model\Resource\Product\Compare\Item $catalogProductCompareItem
     * @param \Magento\Catalog\Helper\Product\Compare $catalogProductCompare
     * @param \Zend_Db_Adapter_Abstract $connection
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\Resource\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Catalog\Model\Resource\Product\Compare\Item $catalogProductCompareItem,
        \Magento\Catalog\Helper\Product\Compare $catalogProductCompare,
        $connection = null
    ) {
        $this->_catalogProductCompareItem = $catalogProductCompareItem;
        $this->_catalogProductCompare = $catalogProductCompare;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection
        );
    }

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\Product\Compare\Item', 'Magento\Catalog\Model\Resource\Product');
        $this->_initTables();
    }

    /**
     * Set customer filter to collection
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->_customerId = (int)$customerId;
        $this->_addJoinToSelect();
        return $this;
    }

    /**
     * Set visitor filter to collection
     *
     * @param int $visitorId
     * @return $this
     */
    public function setVisitorId($visitorId)
    {
        $this->_visitorId = (int)$visitorId;
        $this->_addJoinToSelect();
        return $this;
    }

    /**
     * Retrieve customer filter applied to collection
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_customerId;
    }

    /**
     * Retrieve visitor filter applied to collection
     *
     * @return int
     */
    public function getVisitorId()
    {
        return $this->_visitorId;
    }

    /**
     * Retrieve condition for join filters
     *
     * @return array
     */
    public function getConditionForJoin()
    {
        if ($this->getCustomerId()) {
            return array('customer_id' => $this->getCustomerId());
        }

        if ($this->getVisitorId()) {
            return array('visitor_id' => $this->getVisitorId());
        }

        return array('customer_id' => array('null' => true), 'visitor_id' => '0');
    }

    /**
     * Add join to select
     *
     * @return $this
     */
    public function _addJoinToSelect()
    {
        $this->joinTable(
            array('t_compare' => 'catalog_compare_item'),
            'product_id=entity_id',
            array(
                'product_id' => 'product_id',
                'customer_id' => 'customer_id',
                'visitor_id' => 'visitor_id',
                'item_store_id' => 'store_id',
                'catalog_compare_item_id' => 'catalog_compare_item_id'
            ),
            $this->getConditionForJoin()
        );

        $this->_productLimitationFilters['store_table'] = 't_compare';

        return $this;
    }

    /**
     * Retrieve comapre products attribute set ids
     *
     * @return array
     */
    protected function _getAttributeSetIds()
    {
        // prepare compare items table conditions
        $compareConds = array('compare.product_id=entity.entity_id');
        if ($this->getCustomerId()) {
            $compareConds[] = $this->getConnection()->quoteInto('compare.customer_id = ?', $this->getCustomerId());
        } else {
            $compareConds[] = $this->getConnection()->quoteInto('compare.visitor_id = ?', $this->getVisitorId());
        }

        // prepare website filter
        $websiteId = (int)$this->_storeManager->getStore($this->getStoreId())->getWebsiteId();
        $websiteConds = array(
            'website.product_id = entity.entity_id',
            $this->getConnection()->quoteInto('website.website_id = ?', $websiteId)
        );

        // retrieve attribute sets
        $select = $this->getConnection()->select()->distinct(
            true
        )->from(
            array('entity' => $this->getEntity()->getEntityTable()),
            'attribute_set_id'
        )->join(
            array('website' => $this->getTable('catalog_product_website')),
            join(' AND ', $websiteConds),
            array()
        )->join(
            array('compare' => $this->getTable('catalog_compare_item')),
            join(' AND ', $compareConds),
            array()
        );
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Retrieve attribute ids by set ids
     *
     * @param array $setIds
     * @return array
     */
    protected function _getAttributeIdsBySetIds(array $setIds)
    {
        $select = $this->getConnection()->select()->distinct(
            true
        )->from(
            $this->getTable('eav_entity_attribute'),
            'attribute_id'
        )->where(
            'attribute_set_id IN(?)',
            $setIds
        );
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Retrieve Merged comparable attributes for compared product items
     *
     * @return array
     */
    public function getComparableAttributes()
    {
        if (is_null($this->_comparableAttributes)) {
            $this->_comparableAttributes = array();
            $setIds = $this->_getAttributeSetIds();
            if ($setIds) {
                $attributeIds = $this->_getAttributeIdsBySetIds($setIds);

                $select = $this->getConnection()->select()->from(
                    array('main_table' => $this->getTable('eav_attribute'))
                )->join(
                    array('additional_table' => $this->getTable('catalog_eav_attribute')),
                    'additional_table.attribute_id=main_table.attribute_id'
                )->joinLeft(
                    array('al' => $this->getTable('eav_attribute_label')),
                    'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
                    array(
                        'store_label' => $this->getConnection()->getCheckSql(
                            'al.value IS NULL',
                            'main_table.frontend_label',
                            'al.value'
                        )
                    )
                )->where(
                    'additional_table.is_comparable=?',
                    1
                )->where(
                    'main_table.attribute_id IN(?)',
                    $attributeIds
                );
                $attributesData = $this->getConnection()->fetchAll($select);
                if ($attributesData) {
                    $entityType = \Magento\Catalog\Model\Product::ENTITY;
                    $this->_eavConfig->importAttributesData($entityType, $attributesData);
                    foreach ($attributesData as $data) {
                        $attribute = $this->_eavConfig->getAttribute($entityType, $data['attribute_code']);
                        $this->_comparableAttributes[$attribute->getAttributeCode()] = $attribute;
                    }
                    unset($attributesData);
                }
            }
        }
        return $this->_comparableAttributes;
    }

    /**
     * Load Comparable attributes
     *
     * @return $this
     */
    public function loadComparableAttributes()
    {
        $comparableAttributes = $this->getComparableAttributes();
        $attributes = array();
        foreach ($comparableAttributes as $attribute) {
            $attributes[] = $attribute->getAttributeCode();
        }
        $this->addAttributeToSelect($attributes);

        return $this;
    }

    /**
     * Use product as collection item
     *
     * @return $this
     */
    public function useProductItem()
    {
        $this->setObject('Magento\Catalog\Model\Product');

        $this->setFlag('url_data_object', true);
        $this->setFlag('do_not_use_category_id', true);

        return $this;
    }

    /**
     * Retrieve product ids from collection
     *
     * @return int[]
     */
    public function getProductIds()
    {
        $ids = array();
        foreach ($this->getItems() as $item) {
            $ids[] = $item->getProductId();
        }

        return $ids;
    }

    /**
     * Clear compare items by condition
     *
     * @return $this
     */
    public function clear()
    {
        $this->_catalogProductCompareItem->clearItems($this->getVisitorId(), $this->getCustomerId());
        $this->_eventManager->dispatch('catalog_product_compare_item_collection_clear');

        return $this;
    }

    /**
     * Retrieve is flat enabled flag
     * Overwrite disable flat for compared item if required EAV resource
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        if (!$this->_catalogProductCompare->getAllowUsedFlat()) {
            return false;
        }
        return parent::isEnabledFlat();
    }
}
