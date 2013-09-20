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
 * Cms page mysql resource
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Model_Resource_Page extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Store model
     *
     * @var null|Magento_Core_Model_Store
     */
    protected $_store  = null;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        parent::__construct($resource);
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('cms_page', 'page_id');
    }

    /**
     * Process page data before deleting
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Cms_Model_Resource_Page
     */
    protected function _beforeDelete(Magento_Core_Model_Abstract $object)
    {
        $condition = array(
            'page_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('cms_page_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process page data before saving
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Cms_Model_Resource_Page
     * @throws Magento_Core_Exception
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        /*
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */
        foreach (array('custom_theme_from', 'custom_theme_to') as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->formatDate($value));
        }

        if (!$this->getIsUniquePageToStores($object)) {
            throw new Magento_Core_Exception(__('A page URL key for specified store already exists.'));
        }

        if (!$this->isValidPageIdentifier($object)) {
            throw new Magento_Core_Exception(__('The page URL key contains capital letters or disallowed symbols.'));
        }

        if ($this->isNumericPageIdentifier($object)) {
            throw new Magento_Core_Exception(__('The page URL key cannot be made of only numbers.'));
        }

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('Magento_Core_Model_Date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('Magento_Core_Model_Date')->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Assign page to store views
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Cms_Model_Resource_Page
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('cms_page_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'page_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'page_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param Magento_Core_Model_Abstract $object
     * @param mixed $value
     * @param string $field
     * @return Magento_Cms_Model_Resource_Page
     */
    public function load(Magento_Core_Model_Abstract $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Cms_Model_Resource_Page
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);

        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Magento_Cms_Model_Page $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = array(Magento_Core_Model_AppInterface::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('cms_page_store' => $this->getTable('cms_page_store')),
                $this->getMainTable() . '.page_id = cms_page_store.page_id',
                array())
                ->where('is_active = ?', 1)
                ->where('cms_page_store.store_id IN (?)', $storeIds)
                ->order('cms_page_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Magento_DB_Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('cp' => $this->getMainTable()))
            ->join(
                array('cps' => $this->getTable('cms_page_store')),
                'cp.page_id = cps.page_id',
                array())
            ->where('cp.identifier = ?', $identifier)
            ->where('cps.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('cp.is_active = ?', $isActive);
        }

        return $select;
    }

    /**
     * Check for unique of identifier of page to selected store(s).
     *
     * @param Magento_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniquePageToStores(Magento_Core_Model_Abstract $object)
    {
        if ($this->_storeManager->hasSingleStore() || !$object->hasStores()) {
            $stores = array(Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

        if ($object->getId()) {
            $select->where('cps.page_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     *  Check whether page identifier is numeric
     *
     * @date Wed Mar 26 18:12:28 EET 2008
     *
     * @param Magento_Core_Model_Abstract $object
     * @return bool
     */
    protected function isNumericPageIdentifier(Magento_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether page identifier is valid
     *
     *  @param    Magento_Core_Model_Abstract $object
     *  @return   bool
     */
    protected function isValidPageIdentifier(Magento_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }



    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = array(Magento_Core_Model_AppInterface::ADMIN_STORE_ID, $storeId);
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('cp.page_id')
            ->order('cps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed identifier.
     *
     * @param string $identifier
     * @return string|false
     */
    public function getCmsPageTitleByIdentifier($identifier)
    {
        $stores = array(Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
        if ($this->_store) {
            $stores[] = (int)$this->getStore()->getId();
        }

        $select = $this->_getLoadByIdentifierSelect($identifier, $stores);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('cp.title')
            ->order('cps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getCmsPageTitleById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'title')
            ->where('page_id = :page_id');

        $binds = array(
            'page_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Retrieves cms page identifier from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getCmsPageIdentifierById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'identifier')
            ->where('page_id = :page_id');

        $binds = array(
            'page_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($pageId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('cms_page_store'), 'store_id')
            ->where('page_id = ?',(int)$pageId);

        return $adapter->fetchCol($select);
    }

    /**
     * Set store model
     *
     * @param Magento_Core_Model_Store $store
     * @return Magento_Cms_Model_Resource_Page
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }
}
