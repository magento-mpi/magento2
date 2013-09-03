<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend grid item renderer currency
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Backend_Block_Widget_Grid_Column_Renderer_Currency
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 100;

    /**
     * Currency objects cache
     */
    protected static $_currencies = array();

    /**
     * Application object
     *
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * Locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Directory_Model_Currency_DefaultLocator
     */
    protected $_currencyLocator;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Directory_Model_Currency_DefaultLocator $currencyLocator
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_App $app,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Directory_Model_Currency_DefaultLocator $currencyLocator,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_app = $app;
        $this->_locale = $locale;
        $this->_currencyLocator = $currencyLocator;
    }


    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        if ($data = (string)$row->getData($this->getColumn()->getIndex())) {
            $currency_code = $this->_getCurrencyCode($row);
            $data = floatval($data) * $this->_getRate($row);
            $sign = (bool)(int)$this->getColumn()->getShowNumberSign() && ($data > 0) ? '+' : '';
            $data = sprintf("%f", $data);
            $data = $this->_locale->currency($currency_code)->toCurrency($data);
            return $sign . $data;
        }
        return $this->getColumn()->getDefault();
    }

    /**
     * Returns currency code, false on error
     *
     * @param $row
     * @return string
     */
    protected function _getCurrencyCode($row)
    {
        if ($code = $this->getColumn()->getCurrencyCode()) {
            return $code;
        }
        if ($code = $row->getData($this->getColumn()->getCurrency())) {
            return $code;
        }

        return $this->_currencyLocator->getDefaultCurrency($this->_request);
    }

    /**
     * Get rate for current row, 1 by default
     *
     * @param $row
     * @return float|int
     */
    protected function _getRate($row)
    {
        if ($rate = $this->getColumn()->getRate()) {
            return floatval($rate);
        }
        if ($rate = $row->getData($this->getColumn()->getRateField())) {
            return floatval($rate);
        }
        return $this->_app->getStore()->getBaseCurrency()->getRate($this->_getCurrencyCode($row));
    }

    /**
     * Returns HTML for CSS
     *
     * @return string
     */
    public function renderCss()
    {
        return parent::renderCss() . ' a-right';
    }
}
