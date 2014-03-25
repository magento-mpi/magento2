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
 * Group price model
 */
class GroupPrice extends Price implements \Magento\Catalog\Pricing\Price\OriginPrice
{
    /**
     * @var string
     */
    protected $priceType = 'group_price';
}
