<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Resource\Payment;

/**
 * Paypal transaction resource model
 *
 * @deprecated since 1.6.2.0
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Transaction extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields   = array(
        'additional_information' => array(null, array())
    );

    /**
     * Initialize main table and the primary key field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('paypal_payment_transaction', 'transaction_id');
    }

    /**
     * Load the transaction object by specified txn_id
     *
     * @param \Magento\Paypal\Model\Payment\Transaction $transaction
     * @param string $txnId
     * @return void
     */
    public function loadObjectByTxnId(\Magento\Paypal\Model\Payment\Transaction $transaction, $txnId)
    {
        $select = $this->_getLoadByUniqueKeySelect($txnId);
        $data   = $this->_getWriteAdapter()->fetchRow($select);
        $transaction->setData($data);
        $this->unserializeFields($transaction);
        $this->_afterLoad($transaction);
    }

    /**
     * Serialize additional information, if any
     *
     * @param \Magento\Core\Model\AbstractModel $transaction
     * @return $this
     * @throws \Magento\Core\Exception
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $transaction)
    {
        $txnId       = $transaction->getData('txn_id');
        $idFieldName = $this->getIdFieldName();

        // make sure unique key won't cause trouble
        if ($transaction->isFailsafe()) {
            $autoincrementId = (int)$this->_lookupByTxnId($txnId, $idFieldName);
            if ($autoincrementId) {
                $transaction->setData($idFieldName, $autoincrementId)->isObjectNew(false);
            }
        }

        return parent::_beforeSave($transaction);
    }

    /**
     * Load cell/row by specified unique key parts
     *
     * @param string $txnId
     * @param array|string|object $columns
     * @param bool $isRow
     * @return array|string
     */
    private function _lookupByTxnId($txnId, $columns, $isRow = false)
    {
        $select = $this->_getLoadByUniqueKeySelect($txnId, $columns);
        if ($isRow) {
            return $this->_getWriteAdapter()->fetchRow($select);
        }
        return $this->_getWriteAdapter()->fetchOne($select);
    }

    /**
     * Get select object for loading transaction by the unique key of order_id, payment_id, txn_id
     *
     * @param string $txnId
     * @param string|array|Zend_Db_Expr $columns
     * @return \Magento\DB\Select
     */
    private function _getLoadByUniqueKeySelect($txnId, $columns = '*')
    {
        return $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), $columns)
            ->where('txn_id = ?', $txnId);
    }
}
