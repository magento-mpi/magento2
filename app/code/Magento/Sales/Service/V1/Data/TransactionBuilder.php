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
class TransactionBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * {@inheritdoc}
     */
    public function setTransactionId($transactionId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::TRANSACTION_ID, (int)$transactionId);
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
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::ORDER_ID, (int)$orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentId($paymentId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::PAYMENT_ID, (int)$paymentId);
    }

    /**
     * {@inheritdoc}
     */
    public function setTxnId($txnId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::TXN_ID, (string)$txnId);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentTxnId($parentTxnId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::PARENT_TXN_ID, (string)$parentTxnId);
    }

    /**
     * {@inheritdoc}
     */
    public function setTxnType($txnType)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::TXN_TYPE, (string)$txnType);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsClosed($isClosed)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::IS_CLOSED, (int)$isClosed);
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
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::CREATED_AT, (string)$createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::METHOD, (string)$method);
    }

    /**
     * {@inheritdoc}
     */
    public function setIncrementId($incrementId)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::INCREMENT_ID, (string)$incrementId);
    }

    /**
     * {@inheritdoc}
     */
    public function setChildTransactions($childTransactions)
    {
        $this->_set(\Magento\Sales\Service\V1\Data\Transaction::CHILD_TRANSACTIONS, $childTransactions);
    }
}
