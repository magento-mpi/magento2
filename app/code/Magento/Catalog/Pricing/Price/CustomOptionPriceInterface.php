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
     * Flag to indicate the price is for configuration option of a product
     */
    const CONFIGURATION_OPTION_FLAG = 'configuration_option_flag';

    /**
     * Return calculated options
     *
     * @return array
     */
    public function getOptions();
}
