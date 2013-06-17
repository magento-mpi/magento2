<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Sales_Model_Observer_Backend_CatalogProductQuote
{
    /**
     * @var Mage_Sales_Model_Resource_Quote
     */
    protected $_quote;

    /**
     * @param Mage_Sales_Model_Resource_Quote $quote
     */
    public function __construct(Mage_Sales_Model_Resource_Quote $quote)
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
        if ($status != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            $this->_quote->markQuotesRecollect($productId);
        }
    }

    /**
     * Catalog Product After Save (change status process)
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogProductSaveAfter(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_recollectQuotes($product->getId(), $product->getStatus());
    }

    /**
     * Catalog Mass Status update process
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogProductStatusUpdate(Varien_Event_Observer $observer)
    {
        $status = $observer->getEvent()->getStatus();
        $productId  = $observer->getEvent()->getProductId();
        $this->_recollectQuotes($productId, $status);
    }

    /**
     * When deleting product, subtract it from all quotes quantities
     *
     * @param Varien_Event_Observer
     */
    public function subtractQtyFromQuotes($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_quote->substractProductFromQuotes($product);
    }
}
