<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Pricing\Price;

use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Downloadable\Model\Link;
use Magento\Pricing\Adjustment\Calculator;

/**
 * Class LinkPrice Model
 * @package Magento\Downloadable\Pricing\Price
 */
interface LinkPriceInterface
{
    /**
     * Default price type
     */
    const PRICE_TYPE = 'link_price';

    /**
     * @param Link $link
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getLinkAmount(Link $link);
}
