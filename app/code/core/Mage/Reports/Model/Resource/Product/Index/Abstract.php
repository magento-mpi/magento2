<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports Product Index Abstract Resource Model
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Reports_Model_Resource_Product_Index_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Fields List for update in forsedSave
     *
     * @var array
     */
    protected $_fieldsForUpdate    = array('store_id', 'added_at');

    /**
     * Update Customer from visitor (Customer loggin)
     *
     * @param Mage_Reports_Model_Product_Index_Abstract $object
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     */
    public function updateCustomerFromVisitor(Mage_Reports_Model_Product_Index_Abstract $object)
    {
        /**
         * Do nothing if customer not logged in
         */
        if (!$object->getCustomerId()) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('visitor_id = ?', (int)$object->getVisitorId());

        $rowSet = $select->query()->fetchAll();
        foreach ($rowSet as $row) {
            $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('customer_id = ?', (int)$object->getCustomerId())
                ->where('product_id = ?', (int)$row['product_id']);
            $idx = $adapter->fetchRow($select);

            if ($idx) {
                $adapter->delete($this->getMainTable(), array('index_id = ?' => $row['index_id']));
                $where = array('index_id = ?' => $idx['index_id']);
                $data  = array(
                    'visitor_id'    => (int)$object->getVisitorId(),
                    'store_id'      => (int)$object->getStoreId(),
                    'added_at'      => Varien_Date::now(),
                );
            } else {
                $where = array('index_id = ?' => $row['index_id']);
                $data  = array(
                    'customer_id'   => (int)$object->getCustomerId(),
                    'store_id'      => (int)$object->getStoreId(),
                    'added_at'      => Varien_Date::now()
                );
            }

            $adapter->update($this->getMainTable(), $data, $where);

        }

        return $this;
    }

    /**
     * Purge visitor data by customer (logout)
     *
     * @param Mage_Reports_Model_Product_Index_Abstract $object
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     */
    public function purgeVisitorByCustomer(Mage_Reports_Model_Product_Index_Abstract $object)
    {
        /**
         * Do nothing if customer not logged in
         */
        if (!$object->getCustomerId()) {
            return $this;
        }

        $bind   = array('visitor_id'      => null);
        $where  = array('customer_id = ?' => (int)$object->getCustomerId());
        $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);

        return $this;
    }

    /**
     * Save Product Index data (forsed save)
     *
     * @param Mage_Reports_Model_Product_Index_Abstract $object
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     */
    public function save(Mage_Core_Model_Abstract  $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }

        $this->_serializeFields($object);
        $this->_beforeSave($object);
        $this->_checkUnique($object);

        $data = $this->_prepareDataForSave($object);
        unset($data[$this->getIdFieldName()]);

        $writeAdapter = $this->_getWriteAdapter();
        $updateCondition = array('product_id = ?' => $object->getProductId());
        $updateData = $data;
        unset($updateData['product_id']);

        if (!$object->getCustomerId()) {
            $updateCondition['visitor_id = ?'] = $object->getVisitorId();
        } else {
            $writeAdapter->delete($this->getMainTable(), array(
                'product_id = ?'=> $object->getProductId(),
                'customer_id = ?'=> $object->getCustomerId()
            ));
            $updateCondition = $updateCondition + array(
                'customer_id IS NULL',
                'visitor_id = ?' => $object->getVisitorId()
            );
        }

        $affectedRows = $writeAdapter->update($this->getMainTable(), $updateData, $updateCondition);
        if (!$affectedRows) {
            $writeAdapter->insert($this->getMainTable(), $data);
        }

        $this->unserializeFields($object);
        $this->_afterSave($object);

        return $this;
    }


    /**
     * Clean index (visitor)
     *
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     */
    public function clean()
    {
        while (true) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName()))
                ->joinLeft(
                    array('visitor_table' => $this->getTable('log/visitor')),
                    'main_table.visitor_id = visitor_table.visitor_id',
                    array())
                ->where('main_table.visitor_id > ?', 0)
                ->where('visitor_table.visitor_id IS NULL')
                ->limit(100);
            $indexIds = $this->_getReadAdapter()->fetchCol($select);

            if (!$indexIds) {
                break;
            }

            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . ' IN(?)', $indexIds)
            );
        }
        return $this;
    }

    /**
     * Add information about product ids to visitor/customer
     *
     *
     * @param Mage_Reports_Model_Product_Index_Abstract $object
     * @param array $productIds
     * @return Mage_Reports_Model_Resource_Product_Index_Abstract
     */
    public function registerIds(Varien_Object $object, $productIds)
    {
        /**
         * Prepare data for insert statement
         */
        $row   = array(
            'visitor_id'        => (int)$object->getVisitorId(),
            'customer_id'       => (int)$object->getCustomerId(),
            'store_id'          => (int)$object->getStoreId(),
        );

        $addedAt    = Varien_Date::toTimestamp(true);
        $insertData = array();
        foreach ($productIds as $productId) {
            /**
             * Prepare select for unique key check
             */
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), $this->getIdFieldName())
                ->where('visitor_id = ?', $row['visitor_id'])
                ->where('customer_id = ?', $row['customer_id'])
                ->where('store_id = ?', $row['store_id']);

            if ($productId) {
                /**
                 * Add data for insert/update
                 */
                $row['product_id'] = (int)$productId;
                $row['added_at']   = Varien_Date::formatDate($addedAt+1);

                $select->where('product_id = ?', $productId);

                $result = $this->_getReadAdapter()->fetchOne($select);

                /**
                 * If visitor_id isn't exists
                 */
                if (!$result) {
                    /**
                     * Prepare data for insert
                     */
                    $insertData[] = $row;
                }
            }
        }

        /**
         * Insert data
         */
        if (!empty($insertData)) {
            foreach ($insertData as $data) {
                $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
            }
        }

        return $this;
    }
}
