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
 * Container for Magento_Sales_Model_Order_Invoice for invoice variable
 *
 * Container that can restrict access to properties and method
 * with white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Invoice extends Saas_PrintedTemplate_Model_Variable_Abstract_Entity
{
    /**
     * Key for config
     *
     * @see Saas_PrintedTemplate_Model_Variable_Abstract::_setListsFromConfig()
     * @var string
     */
    protected $_type = 'invoice';

    /**
     * Constructor
     *
     * @see Saas_PrintedTemplate_Model_Template_Variable_Abstract::__construct()
     * @param Magento_Sales_Model_Order_Invoice $value Invoice
     */
    public function __construct(Magento_Sales_Model_Order_Invoice $value)
    {
        parent::__construct($value);
        $this->_setListsFromConfig($this->_type);
    }

    /**
     * Set additional data to invoice object
     *
     * @return Saas_PrintedTemplate_Model_Variable_Invoice
     */
    protected function _initVariable()
    {
        $value = $this->_value;

        $value->setDiscountTotal(0);
        if ($value->hasDiscountAmount()) {
            $value->setDiscountTotal($value->getDiscountTotal() + (float)$value->getDiscountAmount());
        }
        if ($value->hasGiftCardsAmount()) {
            $value->setDiscountTotal($value->getDiscountTotal() + (float)$value->getGiftCardsAmount());
        }
        if ($value->hasCustomerBalanceAmount()) {
            $value->setDiscountTotal($value->getDiscountTotal() + (float)$value->getCustomerBalanceAmount());
        }

        if ($value->getGrandTotal() - $value->getTaxAmount() < 0) {
            $value->setGrandTotalExclTax(0);
        } else {
            $value->setGrandTotalExclTax($value->getGrandTotal() - $value->getTaxAmount());
        }

        $value->setShippingTaxRate($this->_getShippingTaxRate());

        $value->setShippingDiscountAmount(
            $value->getShippingAmount() > 0 ? $value->getOrder()->getShippingDiscountAmount() : 0
        );

        $value->setShippingTotal($value->getShippingInclTax() - $value->getShippingDiscountAmount());

        $value->setSubtotalWithShipping($value->getSubtotal() + $value->getShippingAmount());

        return $this;
    }

    /**
     * Get variable "discount_amount_without_tax" value
     * It is a very specific variable which is required for French invoice
     *
     * @return string
     */
    public function getDiscountAmountWithoutTax()
    {
        return $this->_format($this->_getDiscountAmountWithoutTaxRaw(), 'currency');
    }

    /**
     * Get variable "grand_total_excl_tax_without_discount_tax" value
     * It is a very specific variable which is required for French invoice
     *
     * @return string
     */
    public function getGrandTotalExclTaxWithoutDiscountTax()
    {
        return $this->_format($this->_getGrandTotalExclTaxWithoutDiscountTaxRaw(), 'currency');
    }

    /**
     * Get variable "tax_amount_without_discount" value
     * It is a very specific variable which is required for French invoice
     *
     * @return string
     */
    public function getTaxAmountWithoutDiscount()
    {
        if (!$this->_value->hasTaxAmountWithoutDiscount()) {
            $taxWithoutDiscount =
                $this->_value->getGrandTotal() - $this->_getGrandTotalExclTaxWithoutDiscountTaxRaw();
            $this->_value->setTaxAmountWithoutDiscount($taxWithoutDiscount);
        }

        return $this->_format($this->_value->getTaxAmountWithoutDiscount(), 'currency');
    }

    /**
     * Calculate value for variable "discount_amount_without_tax"
     * It is a very specific variable which is required for French invoice
     *
     * @return float
     */
    protected function _getDiscountAmountWithoutTaxRaw()
    {
        if (!$this->_value->hasDiscountAmountWithouTax()) {
            $discountExclTax = 0;
            foreach ($this->_value->getAllItems() as $item) {
                $taxRate = $this->_getItemTaxRealPercent($item) / 100.;
                $discountExclTax += $item->getDiscountAmount() / (1 + $taxRate);
            }
            $this->_value->setDiscountAmountWithouTax($discountExclTax);
        }

        return $this->_value->getDiscountAmountWithouTax();
    }

    /**
     * Calculate value for variable "grand_total_excl_tax_without_discount_tax"
     * It is a very specific variable which is required for French invoice
     *
     * @return float
     */
    protected function _getGrandTotalExclTaxWithoutDiscountTaxRaw()
    {
        if (!$this->_value->hasGrandTotalExclTaxWithoutDiscountTax()) {
            $discountTax = -$this->_value->getDiscountAmount() - $this->_getDiscountAmountWithoutTaxRaw();
            /**
             * Grand total excluding tax without discount tax
             */
            $grandTotal = $this->_value->getGrandTotalExclTax() + $discountTax;
            $this->_value->setGrandTotalExclTaxWithoutDiscountTax($grandTotal);
        }

        return $this->_value->getGrandTotalExclTaxWithoutDiscountTax();
    }

    /**
     * Calculate real tax percent for current item
     *
     * @todo refactor this method to avoid code duplication
     * with Saas_PrintedTemplate_Model_Variable_Item_Invoice::_getItemTaxRealPercent
     *
     * @param Magento_Sales_Model_Order_Invoice_Item $item
     * @return float
     */
    protected function _getItemTaxRealPercent($item)
    {
        $realPercent = 0;

        $taxRates = $item->getTaxRates();
        if ($taxRates && is_array($taxRates)) {
            foreach ($taxRates as $rate) {
                $realPercent += (float) $rate->getRealPercent();
            }
        }

        return $realPercent;
    }

    /**
     * Calculate shipping tax rate
     *
     * @return Saas_PrintedTemplate_Model_Tax_CompoundId
     */
    protected function _getShippingTaxRate()
    {
        $taxCollections = $this->_getTaxCollections();
        $compoundId = Mage::getModel('Saas_PrintedTemplate_Model_Tax_CompoundId');
        $prevPriority = null;

        foreach ($taxCollections['shipping_taxes'] as $shippingRate) {
            if ($prevPriority === null) {
                $prevPriority = $shippingRate->getPriority();
            }
            if ($shippingRate->getPriority() == $prevPriority) {
                $compoundId->addAnd($shippingRate->getPercent());
            } else {
                $compoundId->addAfter($shippingRate->getPercent());
            }
        }

        return $compoundId;
    }
}
