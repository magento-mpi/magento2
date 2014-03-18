<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order;

use Magento\Core\Exception;

/**
 * Order status resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Status extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     *
     * @return void
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
     * @param string $field
     * @param mixed $value
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Zend_Db_Select
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
     * @param \Magento\Core\Model\AbstractModel $status
     * @return array
     */
    public function getStoreLabels(\Magento\Core\Model\AbstractModel $status)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_labelsTable, array('store_id', 'label'))
            ->where('status = ?', $status->getStatus());
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Save status labels per store
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Core\Model\AbstractModel $object)
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
     * @return $this
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
     * @return $this
     * @throws Exception
     */
    public function unassignState($status, $state)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_stateTable, array('qty' => new \Zend_Db_Expr('COUNT(*)')))
            ->where('state = ?', $state);

        if ($this->_getWriteAdapter()->fetchOne($select) == 1) {
            throw new Exception(
                __('The last status can\'t be unassigned from its current state.')
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
