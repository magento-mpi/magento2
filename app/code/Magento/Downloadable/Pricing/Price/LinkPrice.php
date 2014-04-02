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
     * @var string
     */
    protected $priceType = self::PRICE_TYPE;

    /**
     * @param Link $link
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getLinkAmount(Link $link)
    {
        return $this->calculator->getAmount($link->getPrice(), $link->getProduct());
    }
}
