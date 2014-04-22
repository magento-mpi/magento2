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
interface AttributePriceInterface
{
    /**
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueAmount(array $value = array());

    /**
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueModified(array $value = array());
}
