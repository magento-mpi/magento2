<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart abstract block
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Cart;

class AbstractCart extends \Magento\View\Element\Template
{
    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    protected $_customer = null;
    protected $_quote    = null;
    protected $_totals;
    protected $_itemRenders = array();

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_catalogData = $catalogData;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve renderer list
     *
     * @return \Magento\View\Element\RendererList
     */
    protected function _getRendererList()
    {
        return $this->getRendererListName()
            ? $this->getLayout()->getBlock($this->getRendererListName())
            : $this->getChildBlock('renderer.list');
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     *
     * @return \Magento\View\Element\Template
     * @throws \RuntimeException
     */
    public function getItemRenderer($type)
    {
        $rendererList = $this->_getRendererList();
        if (!$rendererList) {
            throw new \RuntimeException('Renderer list for block "' . $this->getNameInLayout() . '" is not defined');
        }
        $overriddenTemplates = $this->getOverriddenTemplates() ?: array();
        $template = isset($overriddenTemplates[$type]) ? $overriddenTemplates[$type] : $this->getRendererTemplate();
        return $rendererList->getRenderer($type, self::DEFAULT_TYPE, $template);
    }

    /**
     * Get logged in customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = $this->_customerSession->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Get active quote
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->_checkoutSession->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Get all cart items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Get item row html
     *
     * @param   \Magento\Sales\Model\Quote\Item $item
     * @return  string
     */
    public function getItemHtml(\Magento\Sales\Model\Quote\Item $item)
    {
        $renderer = $this->getItemRenderer($item->getProductType())->setItem($item);
        return $renderer->toHtml();
    }

    public function getTotals()
    {
        return $this->getTotalsCache();
    }

    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $this->_totals = $this->getQuote()->getTotals();
        }
        return $this->_totals;
    }

    /**
     * Check if can apply msrp to totals
     *
     * @return bool
     */
    public function canApplyMsrp()
    {
        if (!$this->getQuote()->hasCanApplyMsrp() && $this->_catalogData->isMsrpEnabled()) {
            $this->getQuote()->collectTotals();
        }
        return $this->getQuote()->getCanApplyMsrp();
    }
}
