<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Adjustment;

use Magento\Catalog\Model\Product;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Amount\AmountFactory;
use Magento\Pricing\Adjustment\Calculator as CalculatorBase;
use Magento\Bundle\Model\Product\Price;
use Magento\Bundle\Pricing\Price\BundleSelectionFactory;
use Magento\Bundle\Pricing\Price\BundleOptionPrice;

/**
 * Bundle price calculator
 */
class Calculator implements BundleCalculatorInterface
{
    /**
     * @var CalculatorBase
     */
    protected $calculator;

    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @var BundleSelectionFactory
     */
    protected $selectionFactory;

    /**
     * @param CalculatorBase $calculator
     * @param AmountFactory $amountFactory
     * @param BundleSelectionFactory $bundleSelectionFactory
     */
    public function __construct(
        CalculatorBase $calculator,
        AmountFactory $amountFactory,
        BundleSelectionFactory $bundleSelectionFactory
    ) {
        $this->calculator = $calculator;
        $this->amountFactory = $amountFactory;
        $this->selectionFactory = $bundleSelectionFactory;
    }

    /**
     * Get amount for current product which is included price of existing options with minimal price
     *
     * @param float|string $amount
     * @param SaleableInterface $product
     * @param null|string $exclude
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getAmount($amount, SaleableInterface $product, $exclude = null)
    {
        // Get amount for bundle product
        $bundleProductAmount = $this->calculator->getAmount($amount, $product);
        return $this->getOptionsAmount($product, $exclude, true, $bundleProductAmount);
    }

    /**
     * Get amount for current product which is included price of existing options with maximal price
     *
     * @param float $amount
     * @param Product $product
     * @param null $exclude
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaxAmount($amount, Product $product, $exclude = null)
    {
        // Get amount for bundle product
        $bundleProductAmount = $this->calculator->getAmount($amount, $product);
        return $this->getOptionsAmount($product, $exclude, false, $bundleProductAmount);
    }

    /**
     * Option amount calculation for saleable item
     *
     * @param Product $product
     * @param null|string $exclude
     * @param bool $searchMin
     * @param \Magento\Pricing\Amount\AmountInterface|null $bundleProductAmount
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getOptionsAmount(
        Product $product,
        $exclude = null,
        $searchMin = true,
        $bundleProductAmount = null
    ) {
        $amountList = array_merge(
            $bundleProductAmount ? [$bundleProductAmount] : [],
            $this->getSelectionAmounts($product, $searchMin)
        );

        $fullAmount = 0.;
        $adjustments = [];
        /** @var \Magento\Pricing\Amount\AmountInterface $itemAmount */
        foreach ($amountList as $itemAmount) {
            $fullAmount += $itemAmount->getValue();
            foreach ($itemAmount->getAdjustmentAmounts() as $code => $adjustment) {
                if ($exclude === null || $exclude !== $code) {
                    $adjustments[$code] = isset($adjustments[$code]) ? $adjustments[$code] + $adjustment : $adjustment;
                }
            }
        }
        return $this->amountFactory->create($fullAmount, $adjustments);
    }

    /**
     * @param Product $product
     * @param bool $searchMin
     * @return array
     */
    protected function getSelectionAmounts(Product $product, $searchMin)
    {
        $minOptionAmount = false;
        $amountList = [];
        // Flag shows - is it necessary to find minimal option amount in case if all options are not required
        $shouldFindMinOption = false;
        if ($searchMin
            && $product->getPriceType() == Price::PRICE_TYPE_DYNAMIC
            && !$this->hasRequiredOption($product)
        ) {
            $shouldFindMinOption = true;
        }
        $canSkipRequiredOptions = $searchMin && !$shouldFindMinOption;

        /* @var $option \Magento\Bundle\Model\Option */
        foreach ($this->getBundleOptionPrice($product)->getOptions() as $option) {
            if ($this->canSkipOption($option, $canSkipRequiredOptions)) {
                continue;
            }

            // Add amounts for custom options
            $optionsAmounts = $this->processOptions($option, $product, $searchMin);
            if ($shouldFindMinOption
                && (!$minOptionAmount || end($optionsAmounts)->getValue() < $minOptionAmount->getValue())
            ) {
                $minOptionAmount = end($optionsAmounts);
            } elseif (!$shouldFindMinOption) {
                $amountList = array_merge($amountList, $optionsAmounts);
            }
        }
        return $shouldFindMinOption ? [$minOptionAmount] : $amountList;
    }

    /**
     * @param \Magento\Bundle\Model\Option $option
     * @param bool $canSkipRequiredOption
     * @return bool
     */
    protected function canSkipOption($option, $canSkipRequiredOption)
    {
        return !$option->getSelections() || ($canSkipRequiredOption && !$option->getRequired());
    }

    /**
     * @param Product $product
     * @return bool
     */
    protected function hasRequiredOption($product)
    {
        $options = array_filter($this->getBundleOptionPrice($product)->getOptions(), function ($item) {
            return $item->getRequired();
        });
        return !empty($options);
    }

    /**
     * @param Product $product
     * @return BundleOptionPrice
     */
    protected function getBundleOptionPrice(Product $product)
    {
        return $product->getPriceInfo()->getPrice(BundleOptionPrice::PRICE_CODE);
    }

    /**
     * @param \Magento\Bundle\Model\Option $option
     * @param Product $product
     * @param bool $searchMin
     * @return \Magento\Pricing\Amount\AmountInterface[]
     */
    protected function processOptions($option, $product, $searchMin = true)
    {
        $result = [];
        foreach ($option->getSelections() as $selection) {
            /* @var $selection \Magento\Bundle\Model\Selection|\Magento\Catalog\Model\Product */
            if (!$selection->isSalable()) {
                // @todo CatalogInventory Show out of stock Products
                continue;
            }
            $current = $this->getSelection($selection, $product);
            if (empty($result)) {
                $result = [$current];
                continue;
            }
            if ($searchMin && end($result)->getValue() > $current->getValue()) {
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
     * @param \Magento\Bundle\Model\Selection $selection
     * @param Product $product
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function getSelection($selection, $product)
    {
        if ($product->getPriceType() == Price::PRICE_TYPE_FIXED) {
            return $this->createFixedAmount($selection, $product);
        } else {
            return $this->createDynamicAmount($selection, $product);
        }
    }

    /**
     * @param \Magento\Bundle\Model\Selection $selection
     * @param Product $product
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function createDynamicAmount($selection, $product)
    {
        /** @var \Magento\Bundle\Pricing\Price\BundleSelectionPrice $price */
        $price = $this->selectionFactory->create($product, $selection, $selection->getSelectionQty());
        return $price->getAmount();
    }

    /**
     * @param \Magento\Bundle\Model\Selection $selection
     * @param Product $product
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function createFixedAmount($selection, $product)
    {
        $selectionPrice = $this->selectionFactory
            ->create($product, $selection, $selection->getSelectionQty())
            ->getValue();
        return $this->calculator->getAmount($selectionPrice, $product);
    }
}
