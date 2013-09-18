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
 * CMS block model
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Model_Resource_Block extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * @var Magento_Core_Model_Date
     */
    protected $_date;

    /**
     * @param Magento_Core_Model_Date $date
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Magento_Core_Model_Date $date,
        Magento_Core_Model_Resource $resource
    ) {
        $this->_date = $date;
        parent::__construct($resource);
    }


    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('cms_block', 'block_id');
    }

    /**
     * Process block data before deleting
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Cms_Model_Resource_Page
     */
    protected function _beforeDelete(Magento_Core_Model_Abstract $object)
    {
        $condition = array(
            'block_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('cms_block_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Perform operations before object save
     *
     * @param Magento_Cms_Model_Block $object
     * @return Magento_Cms_Model_Resource_Block
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if (!$this->getIsUniqueBlockToStores($object)) {
            Mage::throwException(__('A block identifier with the same properties already exists in the selected store.'));
        }

        if (! $object->getId()) {
            $object->setCreationTime($this->_date->gmtDate());
        }
        $object->setUpdateTime($this->_date->gmtDate());
        return $this;
    }

    /**
     * Perform operations after object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Cms_Model_Resource_Block
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        $table  = $this->getTable('cms_block_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'block_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'block_id'  => (int) $object->getId(),
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
     * @return Magento_Cms_Model_Resource_Block
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
     * @return Magento_Cms_Model_Resource_Block
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Magento_Cms_Model_Block $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = array(
                (int) $object->getStoreId(),
                Magento_Core_Model_AppInterface::ADMIN_STORE_ID,
            );

            $select->join(
                array('cbs' => $this->getTable('cms_block_store')),
                $this->getMainTable().'.block_id = cbs.block_id',
                array('store_id')
            )->where('is_active = ?', 1)
            ->where('cbs.store_id in (?) ', $stores)
            ->order('store_id DESC')
            ->limit(1);
        }

        return $select;
    }

    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @param Magento_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniqueBlockToStores(Magento_Core_Model_Abstract $object)
    {
        if (Mage::app()->hasSingleStore()) {
            $stores = array(Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getReadAdapter()->select()
            ->from(array('cb' => $this->getMainTable()))
            ->join(
                array('cbs' => $this->getTable('cms_block_store')),
                'cb.block_id = cbs.block_id',
                array()
            )->where('cb.identifier = ?', $object->getData('identifier'))
            ->where('cbs.store_id IN (?)', $stores);

        if ($object->getId()) {
            $select->where('cb.block_id <> ?', $object->getId());
        }

        if ($this->_getReadAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('cms_block_store'), 'store_id')
            ->where('block_id = :block_id');

        $binds = array(
            ':block_id' => (int) $id
        );

        return $adapter->fetchCol($select, $binds);
    }
}
