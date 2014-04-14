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

use Magento\Catalog\Pricing\Price\AbstractPrice;
use Magento\Pricing\Adjustment\Calculator;
use Magento\Pricing\Object\SaleableInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Model\ProductFactory;

/**
 * Final price model
 */
class FinalPrice extends AbstractPrice
{
    /**
     * Price type final
     */
    const PRICE_TYPE_CODE = 'final_price';

    /**
     * @var SaleableInterface
     */
    protected $minProduct;

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
     * @return float
     */
    public function getValue()
    {
        return $this->getMinProduct()->getPriceInfo()
            ->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_TYPE_CODE)->getValue();
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
            foreach ($products as $item) {
                $product = clone $item;
                $product->setQty(\Magento\Pricing\PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT);
                $price = $product->getPriceInfo()
                    ->getPrice(FinalPrice::PRICE_TYPE_CODE)
                    ->getValue();
                if (($price !== false) && ($price <= (is_null($minPrice) ? $price : $minPrice))) {
                    $this->minProduct = $product;
                    $minPrice = $price;
                }
            }
        }
        return $this->minProduct;
    }
}
