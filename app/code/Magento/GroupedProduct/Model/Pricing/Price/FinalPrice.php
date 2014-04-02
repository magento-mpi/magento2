<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Pricing\Price;

use Magento\Pricing\Adjustment\Calculator;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Catalog\Pricing\Price\FinalPriceInterface;
use Magento\Pricing\Price\PriceInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Model\ProductFactory;


/**
 * Final price model
 */
class FinalPrice implements FinalPriceInterface, PriceInterface
{
    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_FINAL;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface
     */
    protected $salableItem;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator
     */
    protected $calculator;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var float
     */
    protected $value;

    /**
     * @var float
     */
    protected $maxValue;

    /**
     * @var Grouped
     */
    protected $groupedType;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @param SaleableInterface $salableItem
     * @param $quantity
     * @param Calculator $calculator
     * @param Grouped $groupedType
     * @param ProductFactory $productFactory
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        Calculator $calculator,
        Grouped $groupedType,
        ProductFactory $productFactory
    ) {
        $this->salableItem = $salableItem;
        $this->calculator = $calculator;
        $this->quantity = $quantity;
        $this->groupedType = $groupedType;
        $this->basePrice = $this->salableItem->getPriceInfo()->getPrice(
            \Magento\Catalog\Pricing\Price\BasePrice::PRICE_TYPE_BASE_PRICE);
    }

    /**
     * @return float|bool
     */
    public function getValue()
    {
        $productIds = $this->salableItem->getChildrenIds($this->salableItem->getId());
        foreach($productIds as $productId) {
            $product = $this->productFactory->create()->load($productId);
            $price = $product->getPriceInfo()
                ->getPrice(FinalPriceInterface::PRICE_TYPE_FINAL)
                ->getValue();
            if ($price !== false) {
                $this->value = min($price, is_null($this->value) ?: 0);
            }
        }
        return $this->value;
    }

    /**
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMinimalPrice()
    {
        return $this->calculator->getAmount($this->getValue(), $this->salableItem);
    }

    /**
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        return $this->calculator->getAmount($this->getMaxValue(), $this->salableItem);
    }

    /**
     * @return float
     */
    public function getMaxValue()
    {
        $productIds = $this->salableItem->getChildrenIds($this->salableItem->getId());
        foreach($productIds as $productId) {
            $product = $this->productFactory->create()->load($productId);
            $price = $product->getPriceInfo()
                ->getPrice(FinalPriceInterface::PRICE_TYPE_FINAL)
                ->getValue();
            if ($price !== false) {
                $this->maxValue = max($price, is_null($this->maxValue) ?: 0);
            }
        }
        return $this->maxValue;
    }
}
