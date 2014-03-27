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

use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\PriceInfoInterface;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Class RegularPrice
 */
class RegularPrice implements PriceInterface
{
    /**
     * Default price type
     */
    const PRICE_TYPE_PRICE_DEFAULT = 'regular_price';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_PRICE_DEFAULT;

    /**
     * @var SaleableInterface|\Magento\Catalog\Model\Product
     */
    protected $salableItem;

    /**
     * @var PriceInfoInterface
     */
    protected $priceInfo;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     */
    public function __construct(SaleableInterface $salableItem, $quantity)
    {
        $this->salableItem = $salableItem;
        $this->quantity = $quantity;
        $this->priceInfo = $salableItem->getPriceInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $price = $this->salableItem->getPrice();
        return $price !== null ? $price : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayValue($baseAmount = null, $excludedCode = null)
    {
        return $this->priceInfo->getAmount($baseAmount ?: $this->getValue())->getDisplayAmount($excludedCode);
    }
}
