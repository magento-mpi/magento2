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
     * @param SaleableInterface $saleableItem
     * @param null|string $exclude
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getAmount($amount, SaleableInterface $saleableItem, $exclude = null)
    {
        // Get amount for bundle product
        $bundleProductAmount = $this->calculator->getAmount($amount, $saleableItem);
        return $this->getOptionsAmount($saleableItem, $exclude, true, $bundleProductAmount);
    }

    /**
     * Get amount for current product which is included price of existing options with maximal price
     *
     * @param float $amount
     * @param Product $saleableItem
     * @param null $exclude
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaxAmount($amount, Product $saleableItem, $exclude = null)
    {
        // Get amount for bundle product
        $bundleProductAmount = $this->calculator->getAmount($amount, $saleableItem);
        return $this->getOptionsAmount($saleableItem, $exclude, false, $bundleProductAmount);
    }

    /**
     * Option amount calculation for saleable item
     *
     * @param Product $saleableItem
     * @param null|string $exclude
     * @param bool $searchMin
     * @param \Magento\Pricing\Amount\AmountInterface|null $bundleProductAmount
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getOptionsAmount(
        Product $saleableItem,
        $exclude = null,
        $searchMin = true,
        $bundleProductAmount = null
    ) {
        $amountList = array_merge(
            $bundleProductAmount ? [$bundleProductAmount] : [],
            $this->getSelectionAmounts($saleableItem, $searchMin)
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
     * @param Product $saleableItem
     * @param bool $searchMin
     * @return array
     */
    protected function getSelectionAmounts(Product $saleableItem, $searchMin)
    {
        $minOptionAmount = false;
        $amountList = [];
        // Flag shows - is it necessary to find minimal option amount in case if all options are not required
        $shouldFindMinOption = false;
        if ($searchMin
            && $saleableItem->getPriceType() == Price::PRICE_TYPE_DYNAMIC
            && !$this->hasRequiredOption($saleableItem)
        ) {
            $shouldFindMinOption = true;
        }
        $canSkipRequiredOptions = $searchMin && !$shouldFindMinOption;

        /* @var $option \Magento\Bundle\Model\Option */
        foreach ($this->getBundleOptionPrice($saleableItem)->getOptions() as $option) {
            if ($this->canSkipOption($option, $canSkipRequiredOptions)) {
                continue;
            }

            // Add amounts for custom options
            $optionsAmounts = $this->processOptions($option, $saleableItem, $searchMin);
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
     * @param Product $saleableItem
     * @return bool
     */
    protected function hasRequiredOption($saleableItem)
    {
        $options = array_filter(
            $this->getBundleOptionPrice($saleableItem)->getOptions(),
            function ($item) {
                return $item->getRequired();
            }
        );
        return !empty($options);
    }

    /**
     * @param Product $saleableItem
     * @return BundleOptionPrice
     */
    protected function getBundleOptionPrice(Product $saleableItem)
    {
        return $saleableItem->getPriceInfo()->getPrice(BundleOptionPrice::PRICE_CODE);
    }

    /**
     * @param \Magento\Bundle\Model\Option $option
     * @param Product $saleableItem
     * @param bool $searchMin
     * @return \Magento\Pricing\Amount\AmountInterface[]
     */
    protected function processOptions($option, $saleableItem, $searchMin = true)
    {
        $result = [];
        foreach ($option->getSelections() as $selection) {
            /* @var $selection \Magento\Bundle\Model\Selection|\Magento\Catalog\Model\Product */
            if (!$selection->isSalable()) {
                // @todo CatalogInventory Show out of stock Products
                continue;
            }
            $current = $this->getSelection($selection, $saleableItem);
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
     * @param Product $saleableItem
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function getSelection($selection, $saleableItem)
    {
        if ($saleableItem->getPriceType() == Price::PRICE_TYPE_FIXED) {
            return $this->createFixedAmount($selection, $saleableItem);
        } else {
            return $this->createDynamicAmount($selection, $saleableItem);
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
     * @param Product $saleableItem
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function createFixedAmount($selection, $saleableItem)
    {
        $selectionPrice = $this->selectionFactory
            ->create($saleableItem, $selection, $selection->getSelectionQty())
            ->getValue();
        return $this->calculator->getAmount($selectionPrice, $saleableItem);
    }
}
