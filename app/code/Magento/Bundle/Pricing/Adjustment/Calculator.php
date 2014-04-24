<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Adjustment;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Object\SaleableInterface;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\Adjustment\Calculator as CalculatorBase;
use Magento\Bundle\Model\Product\Price;
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
     * @var \Magento\Bundle\Pricing\BundleOptionService
     */
    protected $optionService;

    /**
     * @param CalculatorBase $calculator
     * @param AmountFactory $amountFactory
     * @param \Magento\Bundle\Pricing\BundleOptionService $optionService
     * @return \Magento\Bundle\Pricing\Adjustment\Calculator
     */
    public function __construct(
        CalculatorBase $calculator,
        AmountFactory $amountFactory,
        \Magento\Bundle\Pricing\BundleOptionService $optionService
    ) {
        $this->calculator = $calculator;
        $this->amountFactory = $amountFactory;
        $this->optionService = $optionService;
    }

    /**
     * Get amount for current product which is included price of existing options with minimal price
     *
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @param null|string $exclude
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
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
     * @param null|string $exclude
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaxAmount($amount, Product $saleableItem, $exclude = null)
    {
        // Get amount for bundle product
        $bundleProductAmount = $this->calculator->getAmount($amount, $saleableItem);
        return $this->getOptionsAmount($saleableItem, $exclude, false, $bundleProductAmount);
    }

    /**
     * Option amount calculation for bundle product
     *
     * @param Product $saleableItem
     * @param null|string $exclude
     * @param bool $searchMin
     * @param \Magento\Framework\Pricing\Amount\AmountInterface|null $bundleProductAmount
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
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
        /** @var \Magento\Framework\Pricing\Amount\AmountInterface $itemAmount */
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
     * @param Product $bundleProduct
     * @param bool $searchMin
     * @return \Magento\Framework\Pricing\Amount\AmountInterface[]
     */
    protected function getSelectionAmounts(Product $bundleProduct, $searchMin)
    {
        $minOptionAmount = false;
        $amountList = [];
        // Flag shows - is it necessary to find minimal option amount in case if all options are not required
        $shouldFindMinOption = false;
        if ($searchMin
            && $bundleProduct->getPriceType() == Price::PRICE_TYPE_DYNAMIC
            && !$this->hasRequiredOption($bundleProduct)
        ) {
            $shouldFindMinOption = true;
        }
        $canSkipRequiredOptions = $searchMin && !$shouldFindMinOption;

        /* @var $option \Magento\Bundle\Model\Option */
        foreach ($this->getBundleOptions($bundleProduct) as $option) {
            if ($this->canSkipOption($option, $canSkipRequiredOptions)) {
                continue;
            }

            // Add amounts for custom options
            $selectionAmountList = $this->optionService->createSelectionAmountList($bundleProduct, $option, true);
            $optionsAmounts = $this->optionService->processOptions($option, $selectionAmountList, $searchMin);
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
     * Check the bundle product for availability of required options
     *
     * @param Product $bundleProduct
     * @return bool
     */
    protected function hasRequiredOption($bundleProduct)
    {
        $options = array_filter(
            $this->getBundleOptions($bundleProduct),
            function ($item) {
                return $item->getRequired();
            }
        );
        return !empty($options);
    }

    /**
     * Get bundle options
     *
     * @param Product $saleableItem
     * @return \Magento\Bundle\Model\Resource\Option\Collection
     */
    protected function getBundleOptions(Product $saleableItem)
    {
        /** @var BundleOptionPrice $bundlePrice */
        $bundlePrice = $saleableItem->getPriceInfo()->getPrice(BundleOptionPrice::PRICE_CODE);
        return $bundlePrice->getOptions();
    }
}
