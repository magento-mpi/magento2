<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Checkout_Block_Cart_Totals extends Magento_Checkout_Block_Cart_Abstract
{
    protected $_totalRenderers;
    protected $_defaultRenderer = 'Magento_Checkout_Block_Total_Default';
    protected $_totals = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->_coreConfig = $coreConfig;
    }

    public function getTotals()
    {
        if (is_null($this->_totals)) {
            return parent::getTotals();
        }
        return $this->_totals;
    }

    public function setTotals($value)
    {
        $this->_totals = $value;
        return $this;
    }

    protected function _getTotalRenderer($code)
    {
        $blockName = $code.'_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            $block = $this->_defaultRenderer;
            $config = $this->_coreConfig->getNode("global/sales/quote/totals/{$code}/renderer");
            if ($config) {
                $block = (string) $config;
            }

            $block = $this->getLayout()->createBlock($block, $blockName);
        }
        /**
         * Transfer totals to renderer
         */
        $block->setTotals($this->getTotals());
        return $block;
    }

    public function renderTotal($total, $area = null, $colspan = 1)
    {
        $code = $total->getCode();
        if ($total->getAs()) {
            $code = $total->getAs();
        }
        return $this->_getTotalRenderer($code)
            ->setTotal($total)
            ->setColspan($colspan)
            ->setRenderingArea(is_null($area) ? -1 : $area)
            ->toHtml();
    }

    /**
     * Render totals html for specific totals area (footer, body)
     *
     * @param   null|string $area
     * @param   int $colspan
     * @return  string
     */
    public function renderTotals($area = null, $colspan = 1)
    {
        $html = '';
        foreach($this->getTotals() as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        return $html;
    }

    /**
     * Check if we have display grand total in base currency
     *
     * @return bool
     */
    public function needDisplayBaseGrandtotal()
    {
        $quote  = $this->getQuote();
        if ($quote->getBaseCurrencyCode() != $quote->getQuoteCurrencyCode()) {
            return true;
        }
        return false;
    }

    /**
     * Get formated in base currency base grand total value
     *
     * @return string
     */
    public function displayBaseGrandtotal()
    {
        $firstTotal = reset($this->_totals);
        if ($firstTotal) {
            $total = $firstTotal->getAddress()->getBaseGrandTotal();
            return Mage::app()->getStore()->getBaseCurrency()->format($total, array(), true);
        }
        return '-';
    }

    /**
     * Get active or custom quote
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        if ($this->getCustomQuote()) {
            return $this->getCustomQuote();
        }

        if (null === $this->_quote) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }
}
