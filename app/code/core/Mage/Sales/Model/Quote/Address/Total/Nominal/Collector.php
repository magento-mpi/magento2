<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal totals collector
 */
class Mage_Sales_Model_Quote_Address_Total_Nominal_Collector extends Mage_Sales_Model_Quote_Address_Total_Collector
{
    /**
     * Conf. node for nominal totals declaration
     *
     * @var string
     */
    protected $_totalsConfigNode = 'global/sales/quote/nominal_totals';

    /**
     * Custom cache key to not confuse with regular totals
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_quote_nominal_collectors';
}
