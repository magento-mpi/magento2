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

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Amount\AmountFactory;
use Magento\Pricing\Adjustment\Calculator as CalculatorBase;
use Magento\Bundle\Model\Product\Price;
use Magento\Bundle\Pricing\Price\BundleOptionPriceInterface;
use Magento\Bundle\Pricing\Price\BundleSelectionFactory;
use Magento\Bundle\Pricing\Price\BundleOptionPrice;

/**
 * Class Calculator
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
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @param null $exclude
     * @return \Magento\Pricing\Amount\AmountInterface|mixed
     */
    public function getAmount($amount, SaleableInterface $saleableItem, $exclude = null)
    {
        return $this->getOptionsAmount($amount, $saleableItem, $exclude, true);
    }

    /**
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @param null $exclude
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaxAmount($amount, SaleableInterface $saleableItem, $exclude = null)
    {
        return $this->getOptionsAmount($amount, $saleableItem, $exclude, false);
    }

    /**
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @param null $exclude
     * @param bool $searchMin
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function getOptionsAmount($amount, SaleableInterface $saleableItem, $exclude = null, $searchMin = true)
    {
        $fullAmount = 0.;
        $adjustments = [];
        $amountList[] = $this->calculator->getAmount($amount, $saleableItem);

        $minOptionAmount = null;
        /* @var $option \Magento\Bundle\Model\Option */
        foreach ($this->getBundleOptionPrice($saleableItem)->getOptions() as $option) {
            if (!$option->getSelections()) {
                continue;
            }
            $optionsAmounts = $this->processOptions($option, $saleableItem, $searchMin);
            if ($searchMin) {
                if ($minOptionAmount === null) {
                    $minOptionAmount = end($optionsAmounts);
                } else if (end($optionsAmounts)->getValue() < $minOptionAmount->getValue()) {
                    $minOptionAmount = end($optionsAmounts);
                }
            } else {
                $amountList = array_merge($amountList, $optionsAmounts);
            }
        }

        if ($searchMin) {
            $amountList[] = $minOptionAmount;
        }

        /** @var \Magento\Pricing\Amount\AmountInterface $itemAmount */
        foreach ($amountList as $itemAmount) {
            $fullAmount += $itemAmount->getValue();
            foreach ($itemAmount->getAdjustmentAmounts() as $code => $adjustment) {
                if ($exclude !== null) {
                    if ($exclude === $code) {
                        continue;
                    }
                }
                if (isset($adjustments[$code])) {
                    $adjustments[$code] += $adjustment;
                } else {
                    $adjustments[$code] = $adjustment;
                }
            }
        }
        return $this->amountFactory->create($fullAmount, $adjustments);
    }

    /**
     * @param SaleableInterface $saleableItem
     * @return BundleOptionPrice
     */
    protected function getBundleOptionPrice(SaleableInterface $saleableItem)
    {
        return $saleableItem->getPriceInfo()->getPrice(BundleOptionPriceInterface::PRICE_TYPE_BUNDLE_OPTION);
    }


    /**
     * @param \Magento\Bundle\Model\Option $option
     * @param SaleableInterface $saleableItem
     * @param bool $searchMin
     * @return \Magento\Pricing\Amount\AmountInterface[]
     */
    protected function processOptions($option, $saleableItem, $searchMin = true)
    {
        $result = [];
        foreach ($option->getSelections() as $selection) {
            /* @var $selection \Magento\Bundle\Model\Selection */
            if (!$selection->isSalable()/* || ($searchMin && !$option->getRequired())*/) {
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
     * @param SaleableInterface $saleableItem
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
     * @param SaleableInterface $saleableItem
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function createDynamicAmount($selection, $saleableItem)
    {
        return $this->selectionFactory->create($saleableItem, $selection, $selection->getSelectionQty())->getAmount();
    }

    /**
     * @param \Magento\Bundle\Model\Selection $selection
     * @param SaleableInterface $saleableItem
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
