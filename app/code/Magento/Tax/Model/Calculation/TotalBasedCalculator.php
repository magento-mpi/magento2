<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation;

use Magento\Tax\Model\Calculation;
use Magento\Customer\Service\V1\Data\Address;
use Magento\Tax\Service\V1\Data\QuoteDetails\Item as QuoteDetailsItem;

class TotalBasedCalculator extends AbstractBasedCalculator
{
    protected function calculateWithTaxInPrice(QuoteDetailsItem $item, $quantity)
    {
        $taxRateRequest = $this->getAddressRateRequest()->setProductClassId($item->getTaxClassId());
        $rate = $this->calculationTool->getRate($taxRateRequest);
        $storeRate = $storeRate = $this->calculationTool->getStoreRate($taxRateRequest, $this->storeId);

        // Calculate $rowTotalInclTax
        $priceInclTax = $this->calculationTool->round($item->getUnitPrice());
        $rowTotalInclTax = $priceInclTax * $quantity;
        if (!$this->isSameRateAsStore($rate, $storeRate)) {
            $priceInclTax = $this->calculatePriceInclTax($priceInclTax, $storeRate, $rate);
            $rowTotalInclTax = $priceInclTax * $quantity;
        }
        $rowTaxExact = $this->calculationTool->calcTaxAmount($rowTotalInclTax, $rate, true, false);
        $rowTax = $this->roundAmount($rowTaxExact, $rate, true);
        $rowTotal = $rowTotalInclTax - $rowTax;
        $price = $this->calculationTool->round($rowTotal / $quantity);

        //Handle discount
        $discountTaxCompensationAmount = 0;
        $applyTaxAfterDiscount = $this->config->applyTaxAfterDiscount($this->storeId);
        $discountAmount = $item->getDiscountAmount();
        if ($discountAmount && $applyTaxAfterDiscount) {
            //TODO: handle originalDiscountAmount
            $taxableAmount = max($rowTotalInclTax - $discountAmount, 0);
            $rowTaxAfterDiscount = $this->calculationTool->calcTaxAmount(
                $taxableAmount,
                $rate,
                true,
                false
            );
            $rowTaxAfterDiscount = $this->roundAmount(
                $rowTaxAfterDiscount,
                $rate,
                true,
                self::KEY_TAX_AFTER_DISCOUNT_DELTA_ROUNDING
            );
            // Set discount tax compensation
            $discountTaxCompensationAmount = $rowTax - $rowTaxAfterDiscount;
            $rowTax = $rowTaxAfterDiscount;
        }

        // Calculate applied taxes
        /** @var  \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax[] $appliedTaxes */
        $appliedTaxes = [];
        $appliedRates = $this->calculationTool->getAppliedRates($taxRateRequest);
        $appliedTaxes = $this->getAppliedTaxes($rowTax, $rate, $appliedRates);

        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setType($item->getType());
        $this->taxDetailsItemBuilder->setRowTax($rowTax);
        $this->taxDetailsItemBuilder->setPrice($price);
        $this->taxDetailsItemBuilder->setPriceInclTax($priceInclTax);
        $this->taxDetailsItemBuilder->setRowTotal($rowTotal);
        $this->taxDetailsItemBuilder->setRowTotalInclTax($rowTotalInclTax);
        $this->taxDetailsItemBuilder->setDiscountTaxCompensationAmount($discountTaxCompensationAmount);
        $this->taxDetailsItemBuilder->setTaxPercent($rate);
        $this->taxDetailsItemBuilder->setAppliedTaxes($appliedTaxes);
        return $this->taxDetailsItemBuilder->create();
    }

    public function calculateWithTaxNotInPrice(QuoteDetailsItem $item, $quantity)
    {
        $taxRateRequest = $this->getAddressRateRequest()->setProductClassId($item->getTaxClassId());
        $rate = $this->calculationTool->getRate($taxRateRequest);
        $appliedRates = $this->calculationTool->getAppliedRates($taxRateRequest);

        $applyTaxAfterDiscount = $this->config->applyTaxAfterDiscount($this->storeId);
        $discountAmount = $item->getDiscountAmount();
        $discountTaxCompensationAmount = 0;

        // Calculate $rowTotal
        $price = $this->calculationTool->round($item->getUnitPrice());
        $rowTotal = $price * $quantity;
        $rowTaxes = [];
        $rowTaxesBeforeDiscount = [];
        $appliedTaxes = [];
        //Apply each tax rate separately
        foreach ($appliedRates as $appliedRate) {
            $taxId = $appliedRate['id'];
            $taxRate = $appliedRate['percent'];
            $rowTaxPerRate = $this->calculationTool->calcTaxAmount($rowTotal, $taxRate, false, false);
            $rowTaxPerRate = $this->roundAmount($rowTaxPerRate, $taxId, false);
            $rowTaxAfterDiscount = $rowTaxPerRate;

            //Handle discount
            if ($discountAmount && $applyTaxAfterDiscount) {
                //TODO: handle originalDiscountAmount
                $taxableAmount = max($rowTotal - $discountAmount, 0);
                $rowTaxAfterDiscount = $this->calculationTool->calcTaxAmount(
                    $taxableAmount,
                    $taxRate,
                    false,
                    false
                );
                $rowTaxAfterDiscount = $this->roundAmount(
                    $rowTaxAfterDiscount,
                    $taxRate,
                    false,
                    self::KEY_TAX_AFTER_DISCOUNT_DELTA_ROUNDING
                );
            }
            $appliedTaxes[$taxId] = $this->getAppliedTax(
                $rowTaxAfterDiscount,
                $appliedRate
            );

            $rowTaxes[] = $rowTaxAfterDiscount;
            $rowTaxesBeforeDiscount[] = $rowTaxPerRate;
        }
        $rowTax = array_sum($rowTaxes);
        $rowTaxBeforeDiscount = array_sum($rowTaxesBeforeDiscount);
        // Set discount tax compensation
        $discountTaxCompensationAmount = $rowTaxBeforeDiscount - $rowTax;
        $rowTotalInclTax = $rowTotal + $rowTaxBeforeDiscount;
        $priceInclTax = $this->calculationTool->round($rowTotalInclTax / $quantity);

        $this->taxDetailsItemBuilder->setCode($item->getCode());
        $this->taxDetailsItemBuilder->setType($item->getType());
        $this->taxDetailsItemBuilder->setRowTax($rowTax);
        $this->taxDetailsItemBuilder->setPrice($price);
        $this->taxDetailsItemBuilder->setPriceInclTax($priceInclTax);
        $this->taxDetailsItemBuilder->setRowTotal($rowTotal);
        $this->taxDetailsItemBuilder->setRowTotalInclTax($rowTotalInclTax);
        $this->taxDetailsItemBuilder->setDiscountTaxCompensationAmount($discountTaxCompensationAmount);
        $this->taxDetailsItemBuilder->setTaxPercent($rate);
        $this->taxDetailsItemBuilder->setAppliedTaxes($appliedTaxes);
        return $this->taxDetailsItemBuilder->create();
    }

    protected function roundAmount($amount, $rate = null, $direction = null, $type = self::KEY_REGULAR_DELTA_ROUNDING)
    {
        return $this->deltaRound($amount, $rate, $direction, $type);
    }
}
