<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\PriceInfo;

use Magento\Pricing\Amount\AmountFactory;
use Magento\Pricing\PriceInfoInterface;
use Magento\Pricing\PriceComposite;
use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\Adjustment\Collection;
use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Price\Collection as PriceCollection;

/**
 * Price info base model
 */
class Base implements PriceInfoInterface
{
    /**
     * @var SaleableInterface
     */
    protected $saleableItem;

    /**
     * @var PriceCollection
     */
    protected $prices;

    /**
     * @var PriceInterface[]
     */

    /**
     * @var Collection
     */
    protected $adjustmentCollection;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @param SaleableInterface $saleableItem
     * @param PriceCollection $prices
     * @param Collection $adjustmentCollection
     * @param AmountFactory $amountFactory
     * @param float $quantity
     */
    public function __construct(
        SaleableInterface $saleableItem,
        PriceCollection $prices,
        Collection $adjustmentCollection,
        AmountFactory $amountFactory,
        $quantity = self::PRODUCT_QUANTITY_DEFAULT
    ) {
        $this->saleableItem = $saleableItem;

        $this->adjustmentCollection = $adjustmentCollection;
        $this->amountFactory = $amountFactory;
        $this->quantity = $quantity;
        $this->prices = $prices;
    }

    /**
     * Returns array of prices
     *
     * @return PriceInterface[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Returns price by code
     *
     * @param string $priceCode
     * @return PriceInterface
     */
    public function getPrice($priceCode)
    {
        return $this->prices->get($priceCode);
    }

    /**
     * Get all registered adjustments
     *
     * @return AdjustmentInterface[]
     */
    public function getAdjustments()
    {
        return $this->adjustmentCollection->getItems();
    }

    /**
     * Get adjustment by code
     *
     * @param string $adjustmentCode
     * @throws \InvalidArgumentException
     * @return AdjustmentInterface
     */
    public function getAdjustment($adjustmentCode)
    {
        return $this->adjustmentCollection->getItemByCode($adjustmentCode);
    }
}
