<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order status resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Status extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Status labels table
     *
     * @var string
     */
    protected $_labelsTable;

    /**
     * Status state table
     *
     * @var string
     */
    protected $_stateTable;

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('sales_order_status', 'status');
        $this->_isPkAutoIncrement = false;
        $this->_labelsTable = $this->getTable('sales_order_status_label');
        $this->_stateTable  = $this->getTable('sales_order_status_state');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param   string $field
     * @param   mixed $value
     * @return  Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        if ($field == 'default_state') {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), array('label'))
                ->join(
                    array('state_table' => $this->_stateTable),
                    $this->getMainTable() . '.status = state_table.status',
                    'status'
                )
                ->where('state_table.state = ?', $value)
                ->order('state_table.is_default DESC')
                ->limit(1);
        } else {
            $select = parent::_getLoadSelect($field, $value, $object);
        }
        return $select;
    }

    /**
     * Store labels getter
     *
     * @param Magento_Core_Model_Abstract $status
     * @return array
     */
    public function getStoreLabels(Magento_Core_Model_Abstract $status)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_labelsTable, array('store_id', 'label'))
            ->where('status = ?', $status->getStatus());
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Save status labels per store
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Mage_Sales_Model_Resource_Order_Status
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        if ($object->hasStoreLabels()) {
            $labels = $object->getStoreLabels();
            $this->_getWriteAdapter()->delete(
                $this->_labelsTable,
                array('status = ?' => $object->getStatus())
            );
            $data = array();
            foreach ($labels as $storeId => $label) {
                if (empty($label)) {
                    continue;
                }
                $data[] = array(
                    'status'    => $object->getStatus(),
                    'store_id'  => $storeId,
                    'label'     => $label
                );
            }
            if (!empty($data)) {
                $this->_getWriteAdapter()->insertMultiple($this->_labelsTable, $data);
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * Assign order status to order state
     *
     * @param string $status
     * @param string $state
     * @param bool $isDefault
     * @return Mage_Sales_Model_Resource_Order_Status
     */
    public function assignState($status, $state, $isDefault)
    {
        if ($isDefault) {
            $this->_getWriteAdapter()->update(
                $this->_stateTable,
                array('is_default' => 0),
                array('state = ?' => $state)
            );
        }
        $this->_getWriteAdapter()->insertOnDuplicate(
            $this->_stateTable,
            array(
                'status'     => $status,
                'state'      => $state,
                'is_default' => (int) $isDefault
            )
        );
        return $this;
    }

    /**
     * Unassign order status from order state
     *
     * @param string $status
     * @param string $state
     * @return Mage_Sales_Model_Resource_Order_Status
     */
    public function unassignState($status, $state)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_stateTable, array('qty' => new Zend_Db_Expr('COUNT(*)')))
            ->where('state = ?', $state);

        if ($this->_getWriteAdapter()->fetchOne($select) == 1) {
            throw new Magento_Core_Exception(
                Mage::helper('Mage_Sales_Helper_Data')->__('The last status can\'t be unassigned from its current state.')
            );
        }
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_stateTable, 'is_default')
            ->where('state = ?', $state)
            ->where('status = ?', $status)
            ->limit(1);
        $isDefault = $this->_getWriteAdapter()->fetchOne($select);
        $this->_getWriteAdapter()->delete(
            $this->_stateTable,
            array(
                'state = ?' => $state,
                'status = ?' => $status
            )
        );

        if ($isDefault) {
            $select = $this->_getWriteAdapter()->select()
                ->from($this->_stateTable, 'status')
                ->where('state = ?', $state)
                ->limit(1);
            $defaultStatus = $this->_getWriteAdapter()->fetchOne($select);
            if ($defaultStatus) {
                $this->_getWriteAdapter()->update(
                    $this->_stateTable,
                    array('is_default' => 1),
                    array(
                        'state = ?' => $state,
                        'status = ?' => $defaultStatus
                    )
                );
            }
        }
        return $this;
    }
}
