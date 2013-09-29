<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal totals collector
 */
class Magento_Sales_Model_Quote_Address_Total_Nominal_Collector extends Magento_Sales_Model_Quote_Address_Total_Collector
{
    /**
     * Config group for nominal totals declaration
     *
     * @var string
     */
    protected $_configGroup = 'nominal_totals';

    /**
     * Custom cache key to not confuse with regular totals
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_quote_nominal_collectors';
}
