<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry entity collection
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftRegistry_Model_Resource_Entity_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @var Magento_GiftRegistry_Model_Attribute_Config
     */
    protected $attributeConfig;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_GiftRegistry_Model_Attribute_Config $attributeConfig
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_GiftRegistry_Model_Resource_HelperFactory $helperFactory
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_GiftRegistry_Model_Attribute_Config $attributeConfig,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_GiftRegistry_Model_Resource_HelperFactory $helperFactory,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
        $this->attributeConfig = $attributeConfig;
        $this->storeManager = $storeManager;
        $this->helperFactory = $helperFactory;
    }

    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_GiftRegistry_Model_Entity', 'Magento_GiftRegistry_Model_Resource_Entity');
    }

    /**
     * Load collection by customer id
     *
     * @param int $id
     * @return Magento_GiftRegistry_Model_Resource_Entity_Collection
     */
    public function filterByCustomerId($id)
    {
        $this->getSelect()->where('main_table.customer_id = ?', (int)$id);
        return $this;
    }

    /**
     * Load collection by customer id
     *
     * @return Magento_GiftRegistry_Model_Resource_Entity_Collection
     */
    public function filterByActive()
    {
        $this->getSelect()->where('main_table.is_active = ?', 1);
        return $this;
    }

    /**
     * Add registry info
     *
     * @return Magento_GiftRegistry_Model_Resource_Entity_Collection
     */
    public function addRegistryInfo()
    {
        $this->_addQtyItemsData();
        $this->_addEventData();
        $this->_addRegistrantData();

        return $this;
    }

    /**
     * Add registry quantity info
     *
     * @return Magento_GiftRegistry_Model_Resource_Entity_Collection
     */
    protected function _addQtyItemsData()
    {
        $select = $this->getConnection()->select()
            ->from(array('item' => $this->getTable('magento_giftregistry_item')), array(
                'entity_id',
                'qty'           => new Zend_Db_Expr('SUM(item.qty)'),
                'qty_fulfilled' => new Zend_Db_Expr('SUM(item.qty_fulfilled)'),
                'qty_remaining' => new Zend_Db_Expr('SUM(item.qty - item.qty_fulfilled)')
            ))
            ->group('entity_id');

        $this->getSelect()->joinLeft(
            array('items' => new Zend_Db_Expr(sprintf('(%s)', $select))),
            'main_table.entity_id = items.entity_id',
            array('qty', 'qty_fulfilled', 'qty_remaining')
        );

        return $this;
    }

    /**
     * Add event info to collection
     *
     * @return Magento_GiftRegistry_Model_Resource_Entity_Collection
     */
    protected function _addEventData()
    {
        $this->getSelect()->joinLeft(
            array('data' => $this->getTable('magento_giftregistry_data')),
            'main_table.entity_id = data.entity_id',
            array('data.event_date')
        );
        return $this;
    }

    /**
     * Add registrant info to collection
     *
     * @return Magento_GiftRegistry_Model_Resource_Entity_Collection
     */
    protected function _addRegistrantData()
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('magento_giftregistry_person'), array('entity_id'))
            ->group('entity_id');

        /** @var Magento_Core_Model_Resource_Helper $helper */
        $helper = $this->helperFactory->create();
        $helper->addGroupConcatColumn($select, 'registrants', array('firstname', 'lastname'), ', ', ' ');

        $this->getSelect()->joinLeft(
            array('person' => new Zend_Db_Expr(sprintf('(%s)', $select))),
            'main_table.entity_id = person.entity_id',
            array('registrants')
        );

        return $this;
    }

    /**
     * Apply search filters
     *
     * @param array $params
     * @return Magento_GiftRegistry_Model_Resource_Entity_Collection
     */
    public function applySearchFilters($params)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select();
        $select->from(array('m' => $this->getMainTable()), array('*'))
            ->where('m.is_public = ?', 1)
            ->where('m.is_active = ?', 1)
            ->where('m.website_id = ?', (int)$this->storeManager->getStore()->getWebsiteId());

        /*
         * Join registry type store label
         */
        $select->joinLeft(
            array('i1' => $this->getTable('magento_giftregistry_type_info')),
            'i1.type_id = m.type_id AND i1.store_id = 0',
            array()
        );
        $typeExpr = $adapter->getCheckSql('i2.label IS NULL', 'i1.label', 'i2.label');
        $select->joinLeft(
            array('i2' => $this->getTable('magento_giftregistry_type_info')),
            $adapter->quoteInto('i2.type_id = m.type_id AND i2.store_id = ?', (int)$this->storeManager->getStore()->getId()),
            array('type' => $typeExpr)
        );

        /*
         * Join registrant data
         */
        $registrantExpr = $adapter->getConcatSql(array('firstname', 'lastname'), ' ');
        $select->joinInner(
            array('p' => $this->getTable('magento_giftregistry_person')),
            'm.entity_id = p.entity_id',
            array('registrant' => $registrantExpr)
        );

        /*
         * Join entity event data
         */
        $select->joinLeft(
            array('d' => $this->getTable('magento_giftregistry_data')),
            'm.entity_id = d.entity_id',
            array('event_date', 'event_location')
        );

        /*
         * Apply search filters
         */
        if (!empty($params['type_id'])) {
            $select->where('m.type_id = ?', (int)$params['type_id']);
        }
        if (!empty($params['id'])) {
            $select->where('m.url_key = ?', $params['id']);
        }
        if (!empty($params['firstname'])) {
            $select->where($adapter->quoteInto('p.firstname LIKE ?', $params['firstname'] . '%'));
        }
        if (!empty($params['lastname'])) {
            $select->where($adapter->quoteInto('p.lastname LIKE ?', $params['lastname'] . '%'));
        }
        if (!empty($params['email'])) {
            $select->where('p.email = ?', $params['email']);
        }

        /*
         * Apply search filters by static attributes
         */
        $config = $this->attributeConfig;
        $staticCodes = $config->getStaticTypesCodes();
        foreach ($staticCodes as $code) {
            if (!empty($params[$code])) {
                $select->where($adapter->quoteInto($code . ' =?', $params[$code]));
            }
        }
        $dateType = $config->getStaticDateType();
        if (!empty($params[$dateType . '_from'])) {
            $select->where($adapter->quoteInto($dateType . ' >= ?', $params[$dateType . '_from']));
        }
        if (!empty($params[$dateType . '_to'])) {
            $select->where($adapter->quoteInto($dateType . ' <= ?', $params[$dateType . '_to']));
        }

        $select->group('m.entity_id');
        $this->getSelect()->reset()->from(
            array('main_table' => new Zend_Db_Expr(sprintf('(%s)', $select))), array('*')
        );

        return $this;
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return Magento_GiftRegistry_Model_Resource_Entity_Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteIds);
        return $this;
    }

    /**
     * Filter collection by specified status
     *
     * @param int $status
     * @return Magento_GiftRegistry_Model_Resource_Entity_Collection
     */
    public function filterByIsActive($status)
    {
        $this->getSelect()->where('main_table.is_active = ?', $status);
        return $this;
    }
}
