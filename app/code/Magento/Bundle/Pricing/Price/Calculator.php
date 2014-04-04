<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Amount\AmountFactory;
use Magento\Pricing\Adjustment\Calculator as CalculatorBase;
use Magento\Bundle\Model\Product\Price;

/**
 * Class Calculator
 */
class Calculator implements CalculatorInterface
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
     * @param $selection
     * @param $saleableItem
     * @return mixed
     */
    protected function getDynamicAmount($selection, $saleableItem)
    {
        return $this->selectionFactory
            ->create($saleableItem, $selection, $selection->getSelectionQty())->getAmount();
    }

    /**
     * @param $selection
     * @param $saleableItem
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function getFixedAmount($selection, $saleableItem)
    {
        $selectionPrice = $this->selectionFactory
            ->create($saleableItem, $selection, $selection->getSelectionQty())
            ->getValue();
        return $this->calculator->getAmount($selectionPrice, $saleableItem);
    }

    /**
     * @param float|string $amount
     * @param SaleableInterface $saleableItem
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getAmount($amount, SaleableInterface $saleableItem)
    {
        return $this->getOptionsAmount($amount, $saleableItem);
    }

    /**
     * @param float $amount
     * @param SaleableInterface $saleableItem
     * @param bool $searchMin
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function getOptionsAmount($amount, SaleableInterface $saleableItem, $searchMin = true)
    {
        $fullAmount = 0.;
        $adjustments = [];
        $amountList[] = $this->calculator->getAmount($amount, $saleableItem);

        /* @var $option \Magento\Bundle\Model\Option */
        foreach ($this->getBundleOptionPrice($saleableItem)->getOptions() as $option) {
            $amountList = array_merge($amountList, $this->processOptions($option, $saleableItem, $searchMin));
        }

        /** @var \Magento\Pricing\Amount\AmountInterface $itemAmount */
        foreach ($amountList as $itemAmount) {
            $fullAmount += $itemAmount->getValue();
            foreach ($itemAmount->getAdjustmentAmounts() as $code => $adjustment) {
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
     * @param $amount
     * @param SaleableInterface $saleableItem
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaxAmount($amount, SaleableInterface $saleableItem)
    {
        return $this->getOptionsAmount($amount, $saleableItem, false);
    }

    /**
     * @param $option
     * @param $saleableItem
     * @param bool $searchMin
     * @return array
     */
    protected function processOptions($option, $saleableItem, $searchMin = true)
    {
        $result = [];
        foreach ($option->getSelections() as $selection) {
            /* @var $selection \Magento\Bundle\Model\Selection */
            if (!$selection->isSalable() || ($searchMin && !$option->getRequired())) {
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
     * @param $selection
     * @param $saleableItem
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    protected function getSelection($selection, $saleableItem)
    {
        if ($saleableItem->getPriceType() == Price::PRICE_TYPE_FIXED) {
            return $this->getFixedAmount($selection, $saleableItem);
        } else {
            return $this->getDynamicAmount($selection, $saleableItem);
        }
    }

    /**
     * @param SaleableInterface $saleableItem
     * @return BundleOptionPrice
     */
    protected function getBundleOptionPrice(SaleableInterface $saleableItem)
    {
        return $saleableItem->getPriceInfo()->getPrice(BundleOptionPriceInterface::PRICE_TYPE_BUNDLE_OPTION);
    }
}
