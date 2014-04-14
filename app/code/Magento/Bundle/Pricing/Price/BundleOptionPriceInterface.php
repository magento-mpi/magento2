<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Pricing\Price;

/**
 * Option price interface
 */
interface BundleOptionPriceInterface
{
    /**
     * Price model code
     */
    const PRICE_TYPE_CODE = 'bundle_option';

    /**
     * Return calculated options
     *
     * @return array
     */
    public function getOptions();

    /**
     * @param \Magento\Bundle\Model\Selection $selection
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getOptionSelectionAmount($selection);
}
