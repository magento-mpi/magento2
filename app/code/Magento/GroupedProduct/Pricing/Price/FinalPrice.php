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
use Magento\Catalog\Model\Product;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

/**
 * Final price model
 */
class FinalPrice extends AbstractPrice
{
    /**
     * Price type final
     */
    const PRICE_CODE = 'final_price';

    /**
     * @var Product
     */
    protected $minProduct;

    /**
     * @param Product $product
     * @param Calculator $calculator
     */
    public function __construct(
        Product $product,
        Calculator $calculator
    ) {
        $this->product = $product;
        $this->calculator = $calculator;
    }

    /**
     * Return minimal product price
     *
     * @return float
     */
    public function getValue()
    {
        return $this->getMinProduct()->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getValue();
    }

    /**
     * Returns product with minimal price
     *
     * @return Product
     */
    public function getMinProduct()
    {
        if (null === $this->minProduct) {
            $products = $this->product->getTypeInstance()->getAssociatedProducts($this->product);
            $minPrice = null;
            foreach ($products as $item) {
                $product = clone $item;
                $product->setQty(\Magento\Pricing\PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT);
                $price = $product->getPriceInfo()
                    ->getPrice(FinalPrice::PRICE_CODE)
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
