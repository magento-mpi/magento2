<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Transaction\Grid;

/**
 * Sales transaction types option array
 */
class TypeList implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\Sales\Model\Order\Payment\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @param \Magento\Sales\Model\Order\Payment\TransactionFactory $transactionFactory
     */
    public function __construct(\Magento\Sales\Model\Order\Payment\TransactionFactory $transactionFactory)
    {
        $this->_transactionFactory = $transactionFactory;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_transactionFactory->create()->getTransactionTypes();
    }
}
