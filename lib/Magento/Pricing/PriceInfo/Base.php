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
use Magento\Pricing\AdjustmentComposite;
use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Price info base model
 */
class Base implements PriceInfoInterface
{
    /**
     * @var SaleableInterface
     */
    protected $product;

    /**
     * @var PriceComposite
     */
    protected $prices;

    /**
     * @var PriceInterface[]
     */
    protected $priceInstances;

    /**
     * @var AdjustmentComposite
     */
    protected $adjustments;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @param SaleableInterface $product
     * @param PriceComposite $prices
     * @param AdjustmentComposite $adjustments
     * @param AmountFactory $amountFactory
     * @param float $quantity
     */
    public function __construct(
        SaleableInterface $product,
        PriceComposite $prices,
        AdjustmentComposite $adjustments,
        AmountFactory $amountFactory,
        $quantity = self::PRODUCT_QUANTITY_DEFAULT
    ) {
        $this->product = $product;
        $this->prices = $prices;
        $this->adjustments = $adjustments;
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
                $this->priceInstances[$code] = $this->prices->createPriceObject($this->product, $code, $this->quantity);
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
                $this->product,
                $priceCode,
                $this->quantity
            );
            return $this->priceInstances[$priceCode];
        } elseif (isset($this->priceInstances[$priceCode]) && $quantity === null) {
            return $this->priceInstances[$priceCode];
        } else {
            return $this->prices->createPriceObject($this->product, $priceCode, $quantity);
        }
    }

    /**
     * Get all registered adjustments
     *
     * @return AdjustmentInterface[]
     */
    public function getAdjustments()
    {
        return $this->adjustments->getAdjustments();
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
        $adjustments = $this->adjustments->getAdjustments();
        if (!isset($adjustments[$adjustmentCode])) {
            throw new \InvalidArgumentException($adjustmentCode . ' is not registered adjustment');
        }
        return $adjustments[$adjustmentCode];
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
                if ($priceModel->getValue()) {
                    $prices[] = $priceModel;
                }
            }
        }
        return $prices;
    }
}
