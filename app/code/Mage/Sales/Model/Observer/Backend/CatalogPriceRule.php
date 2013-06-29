<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Sales_Model_Observer_Backend_CatalogPriceRule
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
     * When applying a catalog price rule, make related quotes recollect on demand
     */
    public function dispatch()
    {
        $this->_quote->markQuotesRecollectOnCatalogRules();
    }
}
