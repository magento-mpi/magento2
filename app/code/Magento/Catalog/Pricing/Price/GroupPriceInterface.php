<?php
/**
 * {license_notice}
 *
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
     * @return array
     */
    public function getStoredGroupPrice();
}
