<?php
/**
 * {license_notice}
 *
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
     * @var \Magento\Payment\Model\Cart\SalesModel\OrderFactory
     */
    protected $_salesModelOrderFactory;

    /**
     * @var \Magento\Payment\Model\Cart\SalesModel\QuoteFactory
     */
    protected $_salesModelQuoteFactory;

    /**
     * @param \Magento\Payment\Model\Cart\SalesModel\OrderFactory $salesModelOrderFactory
     * @param \Magento\Payment\Model\Cart\SalesModel\QuoteFactory $salesModelQuoteFactory
     */
    public function __construct(
        \Magento\Payment\Model\Cart\SalesModel\OrderFactory $salesModelOrderFactory,
        \Magento\Payment\Model\Cart\SalesModel\QuoteFactory $salesModelQuoteFactory
    ) {
        $this->_salesModelOrderFactory = $salesModelOrderFactory;
        $this->_salesModelQuoteFactory = $salesModelQuoteFactory;
    }

    /**
     * Wrap sales model with Magento\Payment\Model\Cart\SalesModel\SalesModelInterface
     *
     * @param \Magento\Sales\Model\Order|\Magento\Sales\Model\Quote $salesModel
     * @return \Magento\Payment\Model\Cart\SalesModel\SalesModelInterface
     * @throws \InvalidArgumentException
     */
    public function create($salesModel)
    {
        if ($salesModel instanceof \Magento\Sales\Model\Quote) {
            return $this->_salesModelQuoteFactory->create(array('salesModel' => $salesModel));
        } else if ($salesModel instanceof \Magento\Sales\Model\Order) {
            return $this->_salesModelOrderFactory->create(array('salesModel' => $salesModel));
        }

        throw new \InvalidArgumentException('Sales model has bad type!');
    }
}
