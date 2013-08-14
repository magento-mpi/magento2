<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Observer_Backend_CatalogPriceRule
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
     * When applying a catalog price rule, make related quotes recollect on demand
     */
    public function dispatch()
    {
        $this->_quote->markQuotesRecollectOnCatalogRules();
    }
}
