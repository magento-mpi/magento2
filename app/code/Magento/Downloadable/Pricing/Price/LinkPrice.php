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

/**
 * Class LinkPrice Model
 *
 * @package Magento\Downloadable\Pricing\Price
 */
class LinkPrice extends RegularPrice implements LinkPriceInterface
{
    /**
     * Default price type
     */
    const PRICE_CODE = 'link_price';

    /**
     * @param Link $link
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getLinkAmount(Link $link)
    {
        return $this->calculator->getAmount($link->getPrice(), $link->getProduct());
    }
}
