<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Pricing\Price;

use Magento\Framework\Pricing\Amount\AmountInterface;

/**
 * Interface CustomOptionPriceInterface for Configurable Product
 *
 */
interface AttributePriceInterface
{
    /**
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueAmount(array $value = []);

    /**
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueModified(array $value = []);
}
