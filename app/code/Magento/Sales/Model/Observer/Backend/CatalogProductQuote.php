<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Observer_Backend_CatalogProductQuote
{
    /**
     * @var Magento_Sales_Model_Resource_Quote
     */
    protected $_quote;

    /**
     * @param Magento_Sales_Model_Resource_Quote $quote
     */
    public function __construct(Magento_Sales_Model_Resource_Quote $quote)
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
        if ($status != Magento_Catalog_Model_Product_Status::STATUS_ENABLED) {
            $this->_quote->markQuotesRecollect($productId);
        }
    }

    /**
     * Catalog Product After Save (change status process)
     *
     * @param Magento_Event_Observer $observer
     */
    public function catalogProductSaveAfter(Magento_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_recollectQuotes($product->getId(), $product->getStatus());
    }

    /**
     * Catalog Mass Status update process
     *
     * @param Magento_Event_Observer $observer
     */
    public function catalogProductStatusUpdate(Magento_Event_Observer $observer)
    {
        $status = $observer->getEvent()->getStatus();
        $productId  = $observer->getEvent()->getProductId();
        $this->_recollectQuotes($productId, $status);
    }

    /**
     * When deleting product, subtract it from all quotes quantities
     *
     * @param Magento_Event_Observer
     */
    public function subtractQtyFromQuotes($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_quote->substractProductFromQuotes($product);
    }
}
