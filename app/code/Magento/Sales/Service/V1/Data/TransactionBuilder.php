<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1\Data;

/**
 * Builder class for \Magento\Sales\Service\V1\Data\Transaction
 */
class TransactionBuilder extends \Magento\Framework\Service\Data\AbstractSimpleObjectBuilder
{
    /**
     * {@inheritdoc}
     */
    public function setTransactionId($transactionId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::TRANSACTION_ID, $transactionId);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($parentId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::PARENT_ID, $parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentId($paymentId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::PAYMENT_ID, $paymentId);
    }

    /**
     * {@inheritdoc}
     */
    public function setTxnId($txnId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::TXN_ID, $txnId);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentTxnId($parentTxnId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::PARENT_TXN_ID, $parentTxnId);
    }

    /**
     * {@inheritdoc}
     */
    public function setTxnType($txnType)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::TXN_TYPE, $txnType);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsClosed($isClosed)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::IS_CLOSED, $isClosed);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalInformation($additionalInformation)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::ADDITIONAL_INFORMATION, $additionalInformation);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::METHOD, $method);
    }

    /**
     * {@inheritdoc}
     */
    public function setIncrementId($incrementId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::INCREMENT_ID, $incrementId);
    }

    /**
     * {@inheritdoc}
     */
    public function setChildTransactions($childTransactions)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::CHILD_TRANSACTIONS, $childTransactions);
    }
}
