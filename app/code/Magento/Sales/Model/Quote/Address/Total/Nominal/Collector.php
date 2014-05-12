<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal totals collector
 */
namespace Magento\Sales\Model\Quote\Address\Total\Nominal;

class Collector extends \Magento\Sales\Model\Quote\Address\Total\Collector
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
