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

class AbstractCart extends \Magento\Core\Block\Template
{
    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    protected $_customer = null;
    protected $_checkout = null;
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
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_catalogData = $catalogData;
    }

    /**
     * Initialize default item renderer
     */
    protected function _prepareLayout()
    {
        if (!$this->getChildBlock(self::DEFAULT_TYPE)) {
            $this->addChild(
                self::DEFAULT_TYPE,
                'Magento\Checkout\Block\Cart\Item\Renderer',
                array('template' => 'cart/item/default.phtml')
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Get renderer block instance by product type code
     *
     * @param  string $type
     * @throws \RuntimeException
     * @return \Magento\Core\Block\AbstractBlock
     */
    public function getItemRenderer($type)
    {
        $renderer = $this->getChildBlock($type) ?: $this->getChildBlock(self::DEFAULT_TYPE);
        if (!$renderer instanceof \Magento\Core\Block) {
            throw new \RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        $renderer->setRenderedBlock($this);
        return $renderer;
    }


    /**
     * Get logged in customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Get checkout session
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        if (null === $this->_checkout) {
            $this->_checkout = \Mage::getSingleton('Magento\Checkout\Model\Session');
        }
        return $this->_checkout;
    }

    /**
     * Get active quote
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->getCheckout()->getQuote();
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
