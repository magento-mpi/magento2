<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Pricing\Price;

use Magento\Downloadable\Model\Link;

/**
 * Class LinkPrice Model
 * @package Magento\Downloadable\Pricing\Price
 */
interface LinkPriceInterface
{
    /**
     * @param Link $link
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getLinkAmount(Link $link);
}
