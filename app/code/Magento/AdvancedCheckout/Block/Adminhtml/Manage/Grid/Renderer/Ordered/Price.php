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
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Grid\Renderer\Ordered;

class Price extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Price
{
    /**
     * Render price for last ordered item
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function render(\Magento\Framework\Object $row)
    {
        // Show base price of product - the real price will be shown when user will configure product (if needed)
        $priceInitial = $row->getProduct()->getPrice() * 1;

        $priceInitial = floatval($priceInitial) * $this->_getRate($row);
        $priceInitial = sprintf("%f", $priceInitial);
        $currencyCode = $this->_getCurrencyCode($row);
        if ($currencyCode) {
            $priceInitial = $this->_localeCurrency->getCurrency($currencyCode)->toCurrency($priceInitial);
        }

        return $priceInitial;
    }
}
