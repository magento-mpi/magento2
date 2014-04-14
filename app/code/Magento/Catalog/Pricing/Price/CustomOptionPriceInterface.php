<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Pricing\Price;

/**
 * Option price interface
 */
interface CustomOptionPriceInterface
{
    /**
     * Price model code
     */
    const PRICE_TYPE_CODE = 'custom_option_price';

    /**
     * Return calculated options
     *
     * @return array
     */
    public function getOptions();
}
