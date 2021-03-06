<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Widget\Grid\Column\Renderer;

/**
 * Currency cell renderer for customerbalance grids
 */
class Currency extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency
{
    /**
     * @var array
     */
    protected $_websiteBaseCurrencyCodes = [];

    /**
     * Get currency code by row data
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    protected function _getCurrencyCode($row)
    {
        $websiteId = $row->getData('website_id');
        $orphanCurrency = $row->getData('base_currency_code');
        if ($orphanCurrency !== null) {
            return $orphanCurrency;
        }
        if (!isset($this->_websiteBaseCurrencyCodes[$websiteId])) {
            $this->_websiteBaseCurrencyCodes[$websiteId] = $this->_storeManager->getWebsite(
                $websiteId
            )->getBaseCurrencyCode();
        }
        return $this->_websiteBaseCurrencyCodes[$websiteId];
    }

    /**
     * Stub getter for exchange rate
     *
     * @param \Magento\Framework\Object $row
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
