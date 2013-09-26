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
class Magento_Checkout_Block_Cart_Abstract extends Magento_Core_Block_Template
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
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogData = null;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_catalogData = $catalogData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize default item renderer
     */
    protected function _prepareLayout()
    {
        if (!$this->getChildBlock(self::DEFAULT_TYPE)) {
            $this->addChild(
                self::DEFAULT_TYPE,
                'Magento_Checkout_Block_Cart_Item_Renderer',
                array('template' => 'cart/item/default.phtml')
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Get renderer block instance by product type code
     *
     * @param  string $type
     * @throws RuntimeException
     * @return Magento_Core_Block_Abstract
     */
    public function getItemRenderer($type)
    {
        $renderer = $this->getChildBlock($type) ?: $this->getChildBlock(self::DEFAULT_TYPE);
        if (!$renderer instanceof Magento_Core_Block) {
            throw new RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        $renderer->setRenderedBlock($this);
        return $renderer;
    }


    /**
     * Get logged in customer
     *
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Get checkout session
     *
     * @return Magento_Checkout_Model_Session
     */
    public function getCheckout()
    {
        if (null === $this->_checkout) {
            $this->_checkout = Mage::getSingleton('Magento_Checkout_Model_Session');
        }
        return $this->_checkout;
    }

    /**
     * Get active quote
     *
     * @return Magento_Sales_Model_Quote
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
     * @param   Magento_Sales_Model_Quote_Item $item
     * @return  string
     */
    public function getItemHtml(Magento_Sales_Model_Quote_Item $item)
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
