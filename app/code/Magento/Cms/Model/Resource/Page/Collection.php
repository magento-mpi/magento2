<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS page collection
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Model_Resource_Page_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;


    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($eventManager, $fetchStrategy, $resource);
        $this->_storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Cms_Model_Page', 'Magento_Cms_Model_Resource_Page');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
        $this->_map['fields']['store']   = 'store_table.store_id';
    }

    /**
     * Returns pairs identifier - title for unique identifiers
     * and pairs identifier|page_id - title for non-unique after first
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $res = array();
        $existingIdentifiers = array();
        foreach ($this as $item) {
            $identifier = $item->getData('identifier');

            $data['value'] = $identifier;
            $data['label'] = $item->getData('title');

            if (in_array($identifier, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData('page_id');
            } else {
                $existingIdentifiers[] = $identifier;
            }

            $res[] = $data;
        }

        return $res;
    }

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return Magento_Cms_Model_Resource_Page_Collection
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return Magento_Cms_Model_Resource_Page_Collection
     */
    protected function _afterLoad()
    {
        if ($this->_previewFlag) {
            $items = $this->getColumnValues('page_id');
            $connection = $this->getConnection();
            if (count($items)) {
                $select = $connection->select()
                        ->from(array('cps'=>$this->getTable('cms_page_store')))
                        ->where('cps.page_id IN (?)', $items);

                if ($result = $connection->fetchPairs($select)) {
                    foreach ($this as $item) {
                        if (!isset($result[$item->getData('page_id')])) {
                            continue;
                        }
                        if ($result[$item->getData('page_id')] == 0) {
                            $stores = $this->_storeManager->getStores(false, true);
                            $storeId = current($stores)->getId();
                            $storeCode = key($stores);
                        } else {
                            $storeId = $result[$item->getData('page_id')];
                            $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                        }
                        $item->setData('_first_store_id', $storeId);
                        $item->setData('store_code', $storeCode);
                    }
                }
            }
        }

        return parent::_afterLoad();
    }

    /**
     * Add filter by store
     *
     * @param int|Magento_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Magento_Cms_Model_Resource_Page_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Magento_Core_Model_Store) {
                $store = array($store->getId());
            }

            if (!is_array($store)) {
                $store = array($store);
            }

            if ($withAdmin) {
                $store[] = Magento_Core_Model_AppInterface::ADMIN_STORE_ID;
            }

            $this->addFilter('store', array('in' => $store), 'public');
        }
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('cms_page_store')),
                'main_table.page_id = store_table.page_id',
                array()
            )->group('main_table.page_id');
        }
        return parent::_renderFiltersBefore();
    }


    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Magento_DB_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();

        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }
}
