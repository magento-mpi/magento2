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
 * Group price interface
 */
interface GroupPriceInterface
{
    /**
     * Price type group
     */
    const PRICE_TYPE_CODE = 'group_price';

    /**
     * @return array
     */
    public function getStoredGroupPrice();
}
