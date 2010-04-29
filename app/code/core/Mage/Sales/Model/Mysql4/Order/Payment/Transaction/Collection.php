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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment transactions collection
 */
class Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection extends Mage_Sales_Model_Mysql4_Order_Collection_Abstract
{
    /**
     * Order ID filter
     *
     * @var int
     */
    protected $_orderId = null;

    /**
     * Columns of order info that should be selected
     *
     * @var array
     */
    protected $_addOrderInformation = array();

    /**
     * Columns of payment info that should be selected
     *
     * @var array
     */
    protected $_addPaymentInformation = array();

    /**
     * Payment ID filter
     * @var int
     */
    protected $_paymentId = null;

    /**
     * Parent ID filter
     * @var int
     */
    protected $_parentId = null;

    /**
     * Filter by transaction type
     * @var array
     */
    protected $_txnTypes = null;

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField = 'order_id';

    /**
     * Initialize collection items factory class
     */
    protected function _construct()
    {
        $this->_init('sales/order_payment_transaction');
        return parent::_construct();
    }

    /**
     * Join order information
     *
     * @param array $flag
     * @return Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection
     */
    public function addOrderInformation($keys)
    {
        $this->_addOrderInformation = $keys;
        return $this;
    }

    /**
     * Join payment information
     *
     * @param array $flag
     * @return Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection
     */
    public function addPaymentInformation($keys)
    {
        $this->_addPaymentInformation = $keys;
        return $this;
    }

    /**
     * Order ID filter setter
     *
     * @param int $orderId
     * @return Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection
     */
    public function addOrderIdFilter($orderId)
    {
        $this->_orderId = (int)$orderId;
        return $this;
    }

    /**
     * Payment ID filter setter
     * Can take either the integer id or the payment instance
     * @param Mage_Sales_Model_Order_Payment|int $payment
     * @return Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection
     */
    public function addPaymentIdFilter($payment)
    {
        $id = $payment;
        if (is_object($payment)) {
            $id = $payment->getId();
        }
        $this->_paymentId = (int)$id;
        return $this;
    }

    /**
     * Parent ID filter setter
     * @param int $parentId
     * @return Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection
     */
    public function addParentIdFilter($parentId)
    {
        $this->_parentId = (int)$parentId;
        return $this;
    }

    /**
     * Transaction type filter setter
     * @param array|string $txnType
     * @return Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection
     */
    public function addTxnTypeFilter($txnType)
    {
        if (!is_array($txnType)) {
            $txnType = array($txnType);
        }
        $this->_txnTypes = $txnType;
        return $this;
    }

    /**
     * Prepare filters and load the collection
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        // filters
        if ($this->_paymentId) {
            $this->getSelect()->where('main_table.payment_id = ?', $this->_paymentId);
        }
        if ($this->_parentId) {
            $this->getSelect()->where('main_table.parent_id = ?', $this->_parentId);
        }
        if ($this->_txnTypes) {
            $this->getSelect()->where('main_table.txn_type IN(?)', $this->_txnTypes);
        }
        if ($this->_orderId) {
            $this->getSelect()->where('main_table.order_id = ?', $this->_orderId);
        }
        if ($this->_addPaymentInformation) {
            $this->getSelect()->joinInner(
                array('sop' => $this->getTable('sales/order_payment')),
                'main_table.payment_id = sop.entity_id',
                $this->_addPaymentInformation
            );
        }
        if($this->_addOrderInformation) {
            $this->getSelect()->joinInner(
                array('so' => $this->getTable('sales/order')),
                'main_table.order_id = so.entity_id',
                $this->_addOrderInformation
            );
        }
        return parent::load($printQuery, $logQuery);
    }

    /**
     * Unserialize additional_information in each item
     * @return Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }
        return parent::_afterLoad();
    }
}
