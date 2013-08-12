<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for Magento_Sales_Model_Order_Invoice_Item for invoice item variable
 *
 * Container that can restrict access to properties and method
 * with black list or white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Item_Invoice extends Saas_PrintedTemplate_Model_Variable_Item_Abstract
{
    /**
     * Item variable name
     *
     * @var string
     */
    protected $_itemType = 'item_invoice';

    /**
     * Set additional data to invoice item object
     *
     * @return Saas_PrintedTemplate_Model_Variable_Item_Invoice
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _initVariable()
    {
        $value = $this->_value;
        if (!$value instanceof Magento_Object) {
            return $this;
        }
        if (!$value->hasDiscountAmount()) {
            $value->setDiscountAmount(0);
        }
        if (!$value->hasDiscount() && !$this->_isFloatEqualToZero($value->getQty())) {
            $value->setDiscount($value->getDiscountAmount()/$value->getQty());
        }

        // calculate discount amount without taxes (only if discount is applied on price including tax)
        if (!$value->hasDiscountAmountExclTax()) {
            $discountExclTax = $value->getDiscountAmount();
            if ($this->_isApplyDiscountOnPrinceInclTax()) {
                $discountExclTax /= (1 + $this->_getItemTaxRealPercent() / 100.);
            }
            $value->setDiscountAmountExclTax($discountExclTax);
        }
        if (!$value->hasDiscountExclTax() && !$this->_isFloatEqualToZero($value->getQty())) {
            $value->setDiscountExclTax($value->getDiscountAmountExclTax()/$value->getQty());
        }

        if (!$value->hasPriceInclDiscount() && $value->hasPrice() && $value->hasDiscount()) {
            $value->setPriceInclDiscount($value->getPrice() - $value->getDiscount());
        }
        if (!$value->hasPriceInclDiscountExclTax() && $value->hasPrice() && $value->hasDiscountExclTax()) {
            $value->setPriceInclDiscountExclTax($value->getPrice() - $value->getDiscountExclTax());
        }

        if (!$value->hasRowTotalInclDiscount() && $value->hasRowTotal()) {
            $value->setRowTotalInclDiscount($value->getRowTotal() - $value->getDiscountAmount());
        }
        if (!$value->hasRowTotalInclDiscountExclTax() && $value->hasRowTotal()) {
            $value->setRowTotalInclDiscountExclTax($value->getRowTotal() - $value->getDiscountAmountExclTax());
        }
        if (!$value->hasRowTotalInclDiscountAndTax() && $value->hasRowTotalInclTax()) {
            $value->setRowTotalInclDiscountAndTax($value->getRowTotalInclTax() - $value->getDiscountAmount());
        }

        if (!$this->_isFloatEqualToZero($value->getRowTotal())) {
            $value->setDiscountRate(100. * $value->getDiscountAmount() / $value->getRowTotal());
        } else {
            $value->setDiscountRate(0);
        }

        if (!$value->getPriceInclTax() && !$this->_isFloatEqualToZero($value->getQty())) {
            $value->setPriceInclTax(
                ($value->getRowTotal() + $value->getTaxAmount() + $value->getWeeeTaxAppliedRowAmount())/$value->getQty()
            );
        }

        return $this;
    }

    /**
     * Calculate real tax percent for current item
     *
     * @return float
     */
    protected function _getItemTaxRealPercent()
    {
        $realPercent = 0;

        $taxRates = $this->_value->getTaxRates();
        if ($taxRates && is_array($taxRates)) {
            foreach ($taxRates as $rate) {
                $realPercent += (float) $rate->getRealPercent();
            }
        }

        return $realPercent;
    }

    /**
     * Check is discount has been applied on price including taxes
     *
     * @return bool true if including, false if excluding
     */
    protected function _isApplyDiscountOnPrinceInclTax()
    {
        $taxRates = $this->_value->getTaxRates();
        if ($taxRates && is_array($taxRates) && isset($taxRates[0])) {
            return $taxRates[0]->getIsDiscountOnInclTax();
        }

        return false;
    }

    /**
     * Retrieve item's parent entity
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _getParentEntity()
    {
        return $this->_value->getInvoice();
    }
}
