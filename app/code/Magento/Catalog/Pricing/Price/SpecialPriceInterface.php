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
 * Special price interface
 */
interface SpecialPriceInterface
{
    /**
     * Price type special
     */
    const PRICE_TYPE_SPECIAL = 'special_price';

    /**
     * Returns special price
     *
     * @return float
     */
    public function getSpecialPrice();

    /**
     * Returns starting date of the special price
     *
     * @return mixed
     */
    public function getSpecialFromDate();

    /**
     * Returns end date of the special price
     *
     * @return mixed
     */
    public function getSpecialToDate();

    /**
     * @return bool
     */
    public function isScopeDateInInterval();
}
