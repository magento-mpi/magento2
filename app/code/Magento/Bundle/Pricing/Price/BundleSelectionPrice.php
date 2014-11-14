<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price as CatalogPrice;
use Magento\Catalog\Model\Product;
use Magento\Bundle\Model\Product\Price;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Pricing\Object\SaleableInterface;
use Magento\Framework\Pricing\Price\AbstractPrice;

/**
 * Bundle option price
 */
class BundleSelectionPrice extends AbstractPrice
{
    /**
     * Price model code
     */
    const PRICE_CODE = 'bundle_selection';

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $bundleProduct;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var DiscountCalculator
     */
    protected $discountCalculator;

    /**
     * @var bool
     */
    protected $useRegularPrice;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param SaleableInterface $bundleProduct
     * @param ManagerInterface $eventManager
     * @param DiscountCalculator $discountCalculator
     * @param bool $useRegularPrice
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        SaleableInterface $bundleProduct,
        ManagerInterface $eventManager,
        DiscountCalculator $discountCalculator,
        $useRegularPrice = false
    ) {
        parent::__construct($saleableItem, $quantity, $calculator);
        $this->bundleProduct = $bundleProduct;
        $this->eventManager = $eventManager;
        $this->discountCalculator = $discountCalculator;
        $this->useRegularPrice = $useRegularPrice;
    }

    /**
     * Get the price value for one of selection product
     *
     * @return bool|float
     */
    public function getValue()
    {
        if (null !== $this->value) {
            return $this->value;
        }

        $priceCode = $this->useRegularPrice ? BundleRegularPrice::PRICE_CODE : FinalPrice::PRICE_CODE;
        if ($this->bundleProduct->getPriceType() == Price::PRICE_TYPE_DYNAMIC) {
            $value = $this->priceInfo
                ->getPrice($priceCode)
                ->getValue();
        } else {
            if ($this->product->getSelectionPriceType()) {
                // calculate price for selection type percent
                $price = $this->bundleProduct->getPriceInfo()
                    ->getPrice(CatalogPrice\RegularPrice::PRICE_CODE)
                    ->getValue();
                $product = clone $this->bundleProduct;
                $product->setFinalPrice($price);
                $this->eventManager->dispatch(
                    'catalog_product_get_final_price',
                    array('product' => $product, 'qty' => $this->bundleProduct->getQty())
                );
                $value = $product->getData('final_price') * ($this->product->getSelectionPriceValue() / 100);
            } else {
                // calculate price for selection type fixed
                $value = $this->product->getSelectionPriceValue() * $this->quantity;
            }
        }
        if (!$this->useRegularPrice) {
            $value = $this->discountCalculator->calculateDiscount($this->bundleProduct, $value);
        }
        $this->value = $value;
        return $this->value;
    }
}
