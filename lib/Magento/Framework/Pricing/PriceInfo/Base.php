<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing\PriceInfo;

use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\PriceInfoInterface;
use Magento\Framework\Pricing\PriceComposite;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Adjustment\Collection;
use Magento\Framework\Pricing\Adjustment\AdjustmentInterface;
use Magento\Framework\Pricing\Object\SaleableInterface;

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
     * @var PriceComposite
     */
    protected $prices;

    /**
     * @var PriceInterface[]
     */
    protected $priceInstances;

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
     * @param PriceComposite $prices
     * @param Collection $adjustmentCollection
     * @param AmountFactory $amountFactory
     * @param float $quantity
     */
    public function __construct(
        SaleableInterface $saleableItem,
        PriceComposite $prices,
        Collection $adjustmentCollection,
        AmountFactory $amountFactory,
        $quantity = self::PRODUCT_QUANTITY_DEFAULT
    ) {
        $this->saleableItem = $saleableItem;
        $this->prices = $prices;
        $this->adjustmentCollection = $adjustmentCollection;
        $this->amountFactory = $amountFactory;
        $this->quantity = $quantity;
    }

    /**
     * @return PriceInterface[]
     */
    public function getPrices()
    {
        // check if all prices initialized
        $this->initPrices();
        return $this->priceInstances;
    }

    /**
     * Init price types
     *
     * @return $this
     */
    protected function initPrices()
    {
        $prices = $this->prices->getPriceCodes();
        foreach ($prices as $code) {
            if (!isset($this->priceInstances[$code])) {
                $this->priceInstances[$code] = $this->prices->createPriceObject(
                    $this->saleableItem,
                    $code,
                    $this->quantity
                );
            }
        }
        return $this;
    }

    /**
     * @param string $priceCode
     * @param float|null $quantity
     * @return PriceInterface
     */
    public function getPrice($priceCode, $quantity = null)
    {
        if (!isset($this->priceInstances[$priceCode]) && $quantity === null) {
            $this->priceInstances[$priceCode] = $this->prices->createPriceObject(
                $this->saleableItem,
                $priceCode,
                $this->quantity
            );
            return $this->priceInstances[$priceCode];
        } elseif (isset($this->priceInstances[$priceCode]) && $quantity === null) {
            return $this->priceInstances[$priceCode];
        } else {
            return $this->prices->createPriceObject($this->saleableItem, $priceCode, $quantity);
        }
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

    /**
     * Returns prices included in base price
     *
     * @return array
     */
    public function getPricesIncludedInBase()
    {
        $prices = [];
        foreach ($this->prices->getMetadata() as $code => $price) {
            if (isset($price['include_in_base_price']) && $price['include_in_base_price']) {
                $priceModel = $this->getPrice($code, $this->quantity);
                if ($priceModel->getValue() !== false) {
                    $prices[] = $priceModel;
                }
            }
        }
        return $prices;
    }
}
