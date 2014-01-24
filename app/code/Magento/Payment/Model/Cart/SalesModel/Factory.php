<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory that wraps sales model with adapter interface
 */
namespace Magento\Payment\Model\Cart\SalesModel;

class Factory
{
    /**
     * @var \Magento\Payment\Model\Cart\SalesModel\Adapter\OrderFactory
     */
    protected $_salesModelOrderFactory;

    /**
     * @var \Magento\Payment\Model\Cart\SalesModel\Adapter\QuoteFactory
     */
    protected $_salesModelQuoteFactory;

    /**
     * @param \Magento\Payment\Model\Cart\SalesModel\Adapter\OrderFactory $salesModelOrderFactory
     * @param \Magento\Payment\Model\Cart\SalesModel\Adapter\QuoteFactory $salesModelQuoteFactory
     */
    public function __construct(
        \Magento\Payment\Model\Cart\SalesModel\Adapter\OrderFactory $salesModelOrderFactory,
        \Magento\Payment\Model\Cart\SalesModel\Adapter\QuoteFactory $salesModelQuoteFactory
    ) {
        $this->_salesModelOrderFactory = $salesModelOrderFactory;
        $this->_salesModelQuoteFactory = $salesModelQuoteFactory;
    }

    /**
     * Wrap sales model with Magento\Payment\Model\Cart\SalesModel\Adapter\AdapterInterface
     *
     * @param \Magento\Sales\Model\Order|\Magento\Sales\Model\Quote $salesModel
     * @return \Magento\Payment\Model\Cart\SalesModel\Adapter\AdapterInterface
     * @throws \InvalidArgumentException
     */
    public function get($salesModel)
    {
        if ($salesModel instanceof \Magento\Sales\Model\Quote) {
            return $this->_salesModelQuoteFactory->create(array('salesModel' => $salesModel));
        } else if ($salesModel instanceof \Magento\Sales\Model\Order) {
            return $this->_salesModelOrderFactory->create(array('salesModel' => $salesModel));
        }

        throw new \InvalidArgumentException('Sales model has bad type!');
    }
}
