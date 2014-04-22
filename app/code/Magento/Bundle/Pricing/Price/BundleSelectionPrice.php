<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price as CatalogPrice;
use Magento\Catalog\Model\Product;
use Magento\Bundle\Model\Product\Price;
use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Event\ManagerInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Price\AbstractPrice;

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
     * @var BasePrice
     */
    protected $bundleBasePrice;

    /**
     * Event manager
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param SaleableInterface $bundleProduct
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        SaleableInterface $bundleProduct,
        ManagerInterface $eventManager
    ) {
        parent::__construct($saleableItem, $quantity, $calculator);
        $this->bundleProduct = $bundleProduct;
        $this->bundleBasePrice = $this->bundleProduct->getPriceInfo()
            ->getPrice(CatalogPrice\BasePrice::PRICE_CODE, $this->quantity);
        $this->eventManager = $eventManager;
    }

    /**
     * @return bool|float
     */
    public function getValue()
    {
        if (null !== $this->value) {
            return $this->value;
        }

        if ($this->bundleProduct->getPriceType() == Price::PRICE_TYPE_DYNAMIC) {
            $value = $this->priceInfo
                ->getPrice(FinalPrice::PRICE_CODE, $this->quantity)
                ->getValue();
        } else {
            if ($this->product->getSelectionPriceType()) {
                // calculate price for selection type percent

                $product = clone $this->bundleProduct;
                $price = $product->getPriceInfo()
                    ->getPrice(CatalogPrice\RegularPrice::PRICE_CODE, $this->quantity)
                    ->getValue();
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
        $this->value = $this->bundleBasePrice->calculateBaseValue($value);
        return $this->value;
    }
}
