<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Api\Data;

/**
 * Transaction interface.
 *
 * A transaction is an interaction between a merchant and a customer such as a purchase, a credit, a refund, and so on.
 */
interface TransactionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Transaction ID.
     */
    const TRANSACTION_ID = 'transaction_id';
    /*
     * Parent ID.
     */
    const PARENT_ID = 'parent_id';
    /*
     * Order ID.
     */
    const ORDER_ID = 'order_id';
    /*
     * Payment ID.
     */
    const PAYMENT_ID = 'payment_id';
    /*
     * Transaction business ID.
     */
    const TXN_ID = 'txn_id';
    /*
     * Parent transaction ID.
     */
    const PARENT_TXN_ID = 'parent_txn_id';
    /*
     * Transaction type.
     */
    const TXN_TYPE = 'txn_type';
    /*
     * Is closed flag.
     */
    const IS_CLOSED = 'is_closed';
    /*
     * Additional information.
     */
    const ADDITIONAL_INFORMATION = 'additional_information';
    /*
     * Created-at timestamp.
     */
    const CREATED_AT = 'created_at';
    /*
     * Method.
     */
    const METHOD = 'method';
    /*
     * Increment ID.
     */
    const INCREMENT_ID = 'increment_id';
    /*
     * Child transactions.
     */
    const CHILD_TRANSACTIONS = 'child_transactions';

    /**
     * Gets the transaction ID for the transaction.
     *
     * @return int Transaction ID.
     */
    public function getTransactionId();

    /**
     * Gets the parent ID for the transaction.
     *
     * @return int|null The parent ID for the transaction. Otherwise, null.
     */
    public function getParentId();

    /**
     * Gets the order ID for the transaction.
     *
     * @return int Order ID.
     */
    public function getOrderId();

    /**
     * Gets the payment ID for the transaction.
     *
     * @return int Payment ID.
     */
    public function getPaymentId();

    /**
     * Gets the transaction business ID for the transaction.
     *
     * @return string Transaction business ID.
     */
    public function getTxnId();

    /**
     * Gets the parent transaction business ID for the transaction.
     *
     * @return string Parent transaction business ID.
     */
    public function getParentTxnId();

    /**
     * Gets the transaction type for the transaction.
     *
     * @return string Transaction type.
     */
    public function getTxnType();

    /**
     * Gets the value of the is-closed flag for the transaction.
     *
     * @return int Is-closed flag value.
     */
    public function getIsClosed();

    /**
     * Gets any additional information for the transaction.
     *
     * @return string[]|null Array of additional information. Otherwise, null.
     */
    public function getAdditionalInformation();

    /**
     * Gets the created-at timestamp for the transaction.
     *
     * @return string Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Gets an array of child transactions for the transaction.
     *
     * @return \Magento\Sales\Api\Data\TransactionInterface[] Array of child transactions.
     */
    public function getChildTransactions();
}
