<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Pricing\Price\PriceInterface;

interface FinalPriceInterface extends PriceInterface
{
    public function hasDefaultFinalPrice();

    public function hasMinMaxPrices();
}
