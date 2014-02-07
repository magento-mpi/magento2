<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

/**
 * Backend grid item renderer currency
 */
class Price
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var int
     */
    protected $_defaultWidth = 100;

    /**
     * Currency objects cache
     *
     * @var \Magento\Object[]
     */
    protected static $_currencies = array();

    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $currencyCode = $this->_getCurrencyCode($row);

            if (!$currencyCode) {
                return $data;
            }

            $data = floatval($data) * $this->_getRate($row);
            $data = sprintf("%f", $data);
            $data = $this->_locale->currency($currencyCode)->toCurrency($data);
            return $data;
        }
        return $this->getColumn()->getDefault();
    }

    /**
     * Returns currency code for the row, false on error
     *
     * @param \Magento\Object $row
     * @return string|bool
     */
    protected function _getCurrencyCode($row)
    {
        if ($code = $this->getColumn()->getCurrencyCode()) {
            return $code;
        }
        if ($code = $row->getData($this->getColumn()->getCurrency())) {
            return $code;
        }
        return false;
    }

    /**
     * Returns rate for the row, 1 by default
     *
     * @param \Magento\Object $row
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
        return 1;
    }

    /**
     * Renders CSS
     *
     * @return string
     */
    public function renderCss()
    {
        return parent::renderCss() . ' col-price';
    }
}
