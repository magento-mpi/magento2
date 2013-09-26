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
namespace Magento\CustomerBalance\Block\Adminhtml\Widget\Grid\Column\Renderer;

class Currency
extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Currency
{
    /**
     * @var array
     */
    protected $_websiteBaseCurrencyCodes = array();

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $app, $locale, $currencyLocator, $data);
    }

    /**
     * Get currency code by row data
     *
     * @param \Magento\Object $row
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
            $this->_websiteBaseCurrencyCodes[$websiteId] = $this->_storeManager->getWebsite($websiteId)
                ->getBaseCurrencyCode();
        }
        return $this->_websiteBaseCurrencyCodes[$websiteId];
    }

    /**
     * Stub getter for exchange rate
     *
     * @param \Magento\Object $row
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
