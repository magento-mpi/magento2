<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing;

use Magento\Catalog\Model\Product;
use Magento\Pricing\Amount\AmountFactory;
use Magento\Bundle\Pricing\Price\BundleSelectionFactory;
use Magento\Pricing\Adjustment\Calculator;
use Magento\Bundle\Model\Product\Price;

/**
 * Bundle price calculator
 */
class BundleOptionService
{
    /**
     * @var \Magento\Pricing\Amount\AmountFactory
     */
    protected $amountFactory;

    /**
     * @var BundleSelectionFactory
     */
    protected $selectionFactory;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator
     */
    protected $calculator;

    /**
     * @param AmountFactory $amountFactory
     * @param BundleSelectionFactory $bundleSelectionFactory
     * @param Calculator $calculator
     */
    public function __construct(
        AmountFactory $amountFactory,
        BundleSelectionFactory $bundleSelectionFactory,
        Calculator $calculator
    ) {
        $this->amountFactory = $amountFactory;
        $this->selectionFactory = $bundleSelectionFactory;
        $this->calculator = $calculator;
    }

    /**
     * Create amount list for option of bundle selections
     *
     * @param Product $bundleProduct
     * @param \Magento\Bundle\Model\Option $option
     * @param bool $isIncludedPrice
     * @return \Magento\Pricing\Amount\AmountInterface[]
     */
    public function createSelectionAmountList($bundleProduct, $option, $isIncludedPrice = false)
    {
        $amountList = [];
        /* @var $selection \Magento\Bundle\Model\Selection|\Magento\Catalog\Model\Product */
        foreach ($option->getSelections() as $selection) {
            if (!$selection->isSalable()) {
                // @todo CatalogInventory Show out of stock Products
                continue;
            }
            $amountList[] = $this->createSelectionAmount($selection, $bundleProduct, $isIncludedPrice);
        }
        return $amountList;
    }

    /**
     * Find minimal or maximal price for existing options
     *
     * @param \Magento\Bundle\Model\Option $option
     * @param \Magento\Pricing\Amount\AmountInterface[] $selectionAmountList
     * @param bool $searchMin
     * @return \Magento\Pricing\Amount\AmountInterface[]
     */
    public function processOptions($option, $selectionAmountList, $searchMin = true)
    {
        $result = [];
        foreach ($selectionAmountList as $current) {
            if (empty($result)) {
                $result = [$current];
            } elseif ($searchMin && end($result)->getValue() > $current->getValue()) {
                $result = [$current];
            } elseif (!$searchMin && $option->isMultiSelection()) {
                $result[] = $current;
            } elseif (!$searchMin && !$option->isMultiSelection() && end($result)->getValue() < $current->getValue()) {
                $result = [$current];
            }
        }
        return $result;
    }

    /**
     * Create amount for bundle product according to the price type
     *
     * @param \Magento\Bundle\Model\Selection $selection
     * @param Product $bundleProduct
     * @param bool $isIncludedPrice
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function createSelectionAmount($selection, $bundleProduct, $isIncludedPrice = false)
    {
        if ($isIncludedPrice && $bundleProduct->getPriceType() == Price::PRICE_TYPE_FIXED) {
            return $this->createFixedAmount($selection, $bundleProduct);
        } else {
            return $this->createDynamicAmount($selection, $bundleProduct);
        }
    }

    /**
     * Create amount for dynamic bundle product
     *
     * @param \Magento\Bundle\Model\Selection $selection
     * @param Product $bundleProduct
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function createDynamicAmount($selection, $bundleProduct)
    {
        /** @var \Magento\Bundle\Pricing\Price\BundleSelectionPrice $price */
        $price = $this->selectionFactory->create($bundleProduct, $selection, $selection->getSelectionQty());
        return $price->getAmount();
    }

    /**
     * Create amount for fixed bundle product
     *
     * @param \Magento\Bundle\Model\Selection $selection
     * @param Product $bundleProduct
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function createFixedAmount($selection, $bundleProduct)
    {
        $selectionPrice = $this->selectionFactory
            ->create($bundleProduct, $selection, $selection->getSelectionQty())
            ->getValue();
        return $this->calculator->getAmount($selectionPrice, $bundleProduct);
    }
}
