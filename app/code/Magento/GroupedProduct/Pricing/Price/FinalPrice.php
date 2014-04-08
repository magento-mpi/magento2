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
use Magento\Pricing\Amount\AmountInterface;
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
     * @var SaleableInterface
     */
    protected $minProduct;

    /**
     * @var AmountInterface
     */
    protected $amount;

    /**
     * @param SaleableInterface $salableItem
     * @param Calculator $calculator
     */
    public function __construct(
        SaleableInterface $salableItem,
        Calculator $calculator
    ) {
        $this->salableItem = $salableItem;
        $this->calculator = $calculator;
    }

    /**
     * Return minimal product price
     *
     * @return bool
     */
    public function getValue()
    {
        return $this->getMinProduct()->getPriceInfo()
            ->getPrice(\Magento\Catalog\Pricing\Price\FinalPriceInterface::PRICE_TYPE_FINAL)->getValue();
    }

    /**
     * Get price type code
     *
     * @return string
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * Get Price Amount object
     *
     * @return AmountInterface
     */
    public function getAmount()
    {
        if (!$this->amount) {
            $this->amount = $this->calculator->getAmount($this->getValue(), $this->salableItem);
        }
        return $this->amount;
    }

    /**
     * Returns product with minimal price
     *
     * @return SaleableInterface
     */
    public function getMinProduct()
    {
        if (null === $this->minProduct) {
            $products = $this->salableItem->getTypeInstance()->getAssociatedProducts($this->salableItem);
            $minPrice = null;
            foreach($products as $item) {
                $product = clone $item;
                $product->setQty(\Magento\Pricing\PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT);
                $price = $product->getPriceInfo()
                    ->getPrice(FinalPriceInterface::PRICE_TYPE_FINAL)
                    ->getValue();
                if ($price !== false) {
                    if ($price <= (is_null($minPrice) ? $price : $minPrice)) {
                        $this->minProduct = $product;
                        $minPrice = $price;
                    }
                }
            }
        }
        return $this->minProduct;
    }

    /**
     * @param $amount
     * @param null $exclude
     * @return AmountInterface
     */
    public function getCustomAmount($amount = null, $exclude = null)
    {
        if ($amount === null) {
            $amount = $this->getValue();
        }
        return $this->calculator->getAmount($amount, $this->salableItem, $exclude);
    }
}
