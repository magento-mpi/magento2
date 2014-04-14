<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Pricing\Price;

use Magento\Pricing\Amount\AmountInterface;

/**
 * Interface CustomOptionPriceInterface for Configurable Product
 *
 * @package Magento\ConfigurableProduct\Pricing\Price
 */
interface CustomOptionPriceInterface
{
    /**
     * Default price type
     */
    const PRICE_TYPE = 'custom_option_price';

    /**
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueAmount(array $value = array());

    /**
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueOldAmount(array $value = array());

} 
