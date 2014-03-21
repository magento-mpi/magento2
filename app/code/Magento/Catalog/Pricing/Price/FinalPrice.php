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
 * Final price model
 */
class FinalPrice extends AbstractPrice
{
    /**
     * @var string
     */
    protected $priceType = 'final_price';

    /**
     * @return float|int
     */
    public function getMinimalPrice()
    {
        return 0;
    }

    /**
     * @return float|int
     */
    public function getMaximalPrice()
    {
        return 0;
    }
}
