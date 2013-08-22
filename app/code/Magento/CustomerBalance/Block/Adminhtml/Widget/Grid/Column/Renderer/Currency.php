<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency cell renderer for customerbalance grids
 *
 */
class Magento_CustomerBalance_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency
extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency
{
    /**
     * @var array
     */
    protected static $_websiteBaseCurrencyCodes = array();

    /**
     * Get currency code by row data
     *
     * @param Magento_Object $row
     * @return string
     */
    protected function _getCurrencyCode($row)
    {
        $websiteId = $row->getData('website_id');
        $orphanCurrency = $row->getData('base_currency_code');
        if ($orphanCurrency !== null) {
            return $orphanCurrency;
        }
        if (!isset(self::$_websiteBaseCurrencyCodes[$websiteId])) {
            self::$_websiteBaseCurrencyCodes[$websiteId] = Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode();
        }
        return self::$_websiteBaseCurrencyCodes[$websiteId];
    }

    /**
     * Stub getter for exchange rate
     *
     * @param Magento_Object $row
     * @return int
     */
    protected function _getRate($row)
    {
        return 1;
    }

    /**
     * Returns HTML for CSS
     *
     * @return string
     */
    public function renderCss()
    {
        return $this->getColumn()->getCssClass() . ' a-left';
    }
}
