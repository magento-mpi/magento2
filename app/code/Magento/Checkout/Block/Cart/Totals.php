<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Cart;

use Magento\Customer\Service\V1\CustomerServiceInterface as CustomerService;

class Totals extends \Magento\Checkout\Block\Cart\AbstractCart
{
    protected $_totalRenderers;
    protected $_defaultRenderer = 'Magento\Checkout\Block\Total\DefaultTotal';
    protected $_totals = null;

    /**
     * @var \Magento\Sales\Model\Config
     */
    protected $_salesConfig;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param CustomerService $customerService
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        CustomerService $customerService,
        \Magento\Sales\Model\Config $salesConfig,
        array $data = array()
    ) {
        $this->_salesConfig = $salesConfig;
        parent::__construct($context, $catalogData, $customerSession, $checkoutSession, $customerService, $data);
        $this->_isScopePrivate = true;

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
        $blockName = $code . '_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            $renderer = $this->_salesConfig->getTotalsRenderer('quote', 'totals', $code);
            if (!empty($renderer)) {
                $block = $renderer;
            } else {
                $block = $this->_defaultRenderer;
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
            return $this->_storeManager->getStore()->getBaseCurrency()->format($total, array(), true);
        }
        return '-';
    }

    /**
     * Get active or custom quote
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        if ($this->getCustomQuote()) {
            return $this->getCustomQuote();
        }

        if (null === $this->_quote) {
            $this->_quote = $this->_checkoutSession->getQuote();
        }
        return $this->_quote;
    }
}
