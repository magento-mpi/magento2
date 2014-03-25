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
class Price extends AbstractPrice implements \Magento\Catalog\Pricing\Price\OriginPrice
{
    /**
     * @var string
     */
    protected $priceType = 'price';
}
