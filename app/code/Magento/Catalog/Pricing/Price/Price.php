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
 * Base price model
 */
class Price extends AbstractPrice implements OriginPriceInterface
{
    /**
     * Default price type
     */
    const PRICE_TYPE_PRICE_DEFAULT = 'price';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_PRICE_DEFAULT;
}
