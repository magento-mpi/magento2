<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid product price column custom renderer for last ordered items
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Grid_Renderer_Ordered_Price
    extends Magento_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Price
{
    /**
     * Render price for last ordered item
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        // Show base price of product - the real price will be shown when user will configure product (if needed)
        $priceInitial = $row->getProduct()->getPrice() * 1;

        $priceInitial = floatval($priceInitial) * $this->_getRate($row);
        $priceInitial = sprintf("%f", $priceInitial);
        $currencyCode = $this->_getCurrencyCode($row);
        if ($currencyCode) {
            $priceInitial = $this->_locale->currency($currencyCode)->toCurrency($priceInitial);
        }

        return $priceInitial;
    }
}
