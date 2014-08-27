<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Column\Renderer;

/**
 * Backend grid item renderer currency
 */
class Price extends \Magento\Ui\Listing\Block\Column\Renderer\AbstractRenderer
{
    /**
     * @var int
     */
    protected $_defaultWidth = 100;

    /**
     * Currency objects cache
     *
     * @var \Magento\Framework\Object[]
     */
    protected static $_currencies = array();

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_localeCurrency = $localeCurrency;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function render(\Magento\Framework\Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $currencyCode = $this->_getCurrencyCode($row);

            if (!$currencyCode) {
                return $data;
            }

            $data = floatval($data) * $this->_getRate($row);
            $data = sprintf("%f", $data);
            $data = $this->_localeCurrency->getCurrency($currencyCode)->toCurrency($data);
            return $data;
        }
        return $this->getColumn()->getDefault();
    }

    /**
     * Returns currency code for the row, false on error
     *
     * @param \Magento\Framework\Object $row
     * @return string|false
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
     * @param \Magento\Framework\Object $row
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
