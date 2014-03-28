<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Pricing\Price;

/**
 * Option price interface
 */
interface OptionPriceInterface
{
    /**
     * Price model code
     */
    const PRICE_TYPE_CUSTOM_OPTION = 'custom_option';

    /**
     * Return calculated options
     *
     * @return array
     */
    public function getOptions();
}
