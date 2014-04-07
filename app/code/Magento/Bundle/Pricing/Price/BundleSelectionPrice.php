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

use Magento\Bundle\Pricing\Adjustment\Calculator;
use Magento\Catalog\Pricing\Price as CatalogPrice;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Pricing\Price\FinalPriceInterface;
use Magento\Pricing\Adjustment\CalculatorInterface;

/**
 * Class OptionPrice
 */
class BundleSelectionPrice extends CatalogPrice\RegularPrice implements BundleSelectionPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_BUNDLE_SELECTION;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $bundleProduct;

    /**
     * Event manager
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param \Magento\Catalog\Model\Product $bundleProduct
     * @param \Magento\Event\ManagerInterface $eventManager
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        CalculatorInterface $calculator,
        \Magento\Catalog\Model\Product $bundleProduct,
        \Magento\Event\ManagerInterface $eventManager
    ) {
        $this->bundleProduct = $bundleProduct;
        $this->eventManager = $eventManager;
        parent::__construct($salableItem, $quantity, $calculator);
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
            $value = $this->salableItem
                ->getPriceInfo()
                ->getPrice(FinalPriceInterface::PRICE_TYPE_FINAL, $this->quantity)
                ->getValue();
        } else {
            if ($this->salableItem->getSelectionPriceType()) {
                // calculate price for selection type percent
                // @todo get rid of final price data manipulation that should fire event to apply catalog rules
                $product = clone $this->bundleProduct;
                $price = $product->getPriceInfo()
                    ->getPrice(CatalogPrice\RegularPrice::PRICE_TYPE_PRICE_DEFAULT, $this->quantity)
                    ->getValue();
                $product->setFinalPrice($price);
                $this->eventManager->dispatch(
                    'catalog_product_get_final_price',
                    array('product' => $product, 'qty' => $this->bundleProduct->getQty())
                );
                $value = $product->getData('final_price') * ($this->salableItem->getSelectionPriceValue() / 100);
            } else {
                // calculate price for selection type fixed
                $value = $this->salableItem->getSelectionPriceValue() * $this->quantity;
            }
        }
        $this->value = $this->bundleProduct->getPriceInfo()
            ->getPrice(CatalogPrice\BasePrice::PRICE_TYPE_BASE_PRICE, $this->quantity)
            ->applyDiscount($value);
        return $this->value;
    }
}
