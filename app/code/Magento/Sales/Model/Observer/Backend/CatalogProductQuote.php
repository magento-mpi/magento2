<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Observer\Backend;

class CatalogProductQuote
{
    /**
     * @var \Magento\Sales\Model\Resource\Quote
     */
    protected $_quote;

    /**
     * @param \Magento\Sales\Model\Resource\Quote $quote
     */
    public function __construct(\Magento\Sales\Model\Resource\Quote $quote)
    {
        $this->_quote = $quote;
    }

    /**
     * Mark recollect contain product(s) quotes
     *
     * @param int $productId
     * @param int $status
     */
    protected function _recollectQuotes($productId, $status)
    {
        if ($status != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
            $this->_quote->markQuotesRecollect($productId);
        }
    }

    /**
     * Catalog Product After Save (change status process)
     *
     * @param \Magento\Event\Observer $observer
     */
    public function catalogProductSaveAfter(\Magento\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_recollectQuotes($product->getId(), $product->getStatus());
    }

    /**
     * When deleting product, subtract it from all quotes quantities
     *
     * @param \Magento\Event\Observer
     */
    public function subtractQtyFromQuotes($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_quote->substractProductFromQuotes($product);
    }
}
