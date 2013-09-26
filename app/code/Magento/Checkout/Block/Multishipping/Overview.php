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
 * Multishipping checkout overview information
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Multishipping_Overview extends Magento_Sales_Block_Items_Abstract
{
    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * @var Magento_Checkout_Model_Type_Multishipping
     */
    protected $_multishipping;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Type_Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Type_Multishipping $multishipping,
        array $data = array()
    ) {
        $this->_multishipping = $multishipping;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize default item renderer
     */
    protected function _prepareLayout()
    {
        $rowItemType = $this->_getRowItemType(self::DEFAULT_TYPE);
        if (!$this->getChildBlock($rowItemType)) {
            $this->addChild(
                $rowItemType,
                'Magento_Checkout_Block_Cart_Item_Renderer',
                array('template' => 'multishipping/overview/item.phtml')
            );
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(
                __('Review Order - %1', $headBlock->getDefaultTitle())
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Get multishipping checkout model
     *
     * @return Magento_Checkout_Model_Type_Multishipping
     */
    public function getCheckout()
    {
        return $this->_multishipping;
    }

    /**
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->getCheckout()->getQuote()->getBillingAddress();
    }

    /**
     * @return string
     */
    public function getPaymentHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Get object with payment info posted data
     *
     * @return Magento_Object
     */
    public function getPayment()
    {
        if (!$this->hasData('payment')) {
            $payment = new Magento_Object($this->getRequest()->getPost('payment'));
            $this->setData('payment', $payment);
        }
        return $this->_getData('payment');
    }

    /**
     * @return array
     */
    public function getShippingAddresses()
    {
        return $this->getCheckout()->getQuote()->getAllShippingAddresses();
    }

    /**
     * @return int|mixed
     */
    public function getShippingAddressCount()
    {
        $count = $this->getData('shipping_address_count');
        if (is_null($count)) {
            $count = count($this->getShippingAddresses());
            $this->setData('shipping_address_count', $count);
        }
        return $count;
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return bool
     */
    public function getShippingAddressRate($address)
    {
        $rate = $address->getShippingRateByCode($address->getShippingMethod());
        if ($rate) {
            return $rate;
        }
        return false;
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return mixed
     */
    public function getShippingPriceInclTax($address)
    {
        $exclTax = $address->getShippingAmount();
        $taxAmount = $address->getShippingTaxAmount();
        return $this->formatPrice($exclTax + $taxAmount);
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return mixed
     */
    public function getShippingPriceExclTax($address)
    {
        return $this->formatPrice($address->getShippingAmount());
    }

    /**
     * @param $price
     * @return mixed
     */
    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return mixed
     */
    public function getShippingAddressItems($address)
    {
        return $address->getAllVisibleItems();
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return mixed
     */
    public function getShippingAddressTotals($address)
    {
        $totals = $address->getTotals();
        foreach ($totals as $total) {
            if ($total->getCode()=='grand_total') {
                if ($address->getAddressType() == Magento_Sales_Model_Quote_Address::TYPE_BILLING) {
                    $total->setTitle(__('Total'));
                }
                else {
                    $total->setTitle(__('Total for this address'));
                }
            }
        }
        return $totals;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->getCheckout()->getQuote()->getGrandTotal();
    }

    /**
     * @return string
     */
    public function getAddressesEditUrl()
    {
        return $this->getUrl('*/*/backtoaddresses');
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return string
     */
    public function getEditShippingAddressUrl($address)
    {
        return $this->getUrl('*/multishipping_address/editShipping', array('id'=>$address->getCustomerAddressId()));
    }

    /**
     * @param $address
     * @return string
     */
    public function getEditBillingAddressUrl($address)
    {
        return $this->getUrl('*/multishipping_address/editBilling', array('id'=>$address->getCustomerAddressId()));
    }

    /**
     * @return string
     */
    public function getEditShippingUrl()
    {
        return $this->getUrl('*/*/backtoshipping');
    }

    /**
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/overviewPost');
    }

    /**
     * @return string
     */
    public function getEditBillingUrl()
    {
        return $this->getUrl('*/*/backtobilling');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/backtobilling');
    }

    /**
     * Retrieve virtual product edit url
     *
     * @return string
     */
    public function getVirtualProductEditUrl()
    {
        return $this->getUrl('*/cart');
    }

    /**
     * Retrieve virtual product collection array
     *
     * @return array
     */
    public function getVirtualItems()
    {
        $items = array();
        foreach ($this->getBillingAddress()->getItemsCollection() as $_item) {
            if ($_item->isDeleted()) {
                continue;
            }
            if ($_item->getProduct()->getIsVirtual() && !$_item->getParentItemId()) {
                $items[] = $_item;
            }
        }
        return $items;
    }

    /**
     * Retrieve quote
     *
     * @return Magento_Sales_Model_Qoute
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * @return mixed
     */
    public function getBillinAddressTotals()
    {
        $_address = $this->getQuote()->getBillingAddress();
        return $this->getShippingAddressTotals($_address);
    }

    /**
     * @param $totals
     * @param null $colspan
     * @return string
     */
    public function renderTotals($totals, $colspan = null)
    {
        if ($colspan === null) {
            $colspan = $this->helper('Magento_Tax_Helper_Data')->displayCartBothPrices() ? 5 : 3;
        }
        $totals = $this->getChildBlock('totals')->setTotals($totals)->renderTotals('', $colspan)
            . $this->getChildBlock('totals')->setTotals($totals)->renderTotals('footer', $colspan);
        return $totals;
    }

    /**
     * Return row-level item html
     *
     * @param Magento_Object $item
     * @return string
     */
    public function getRowItemHtml(Magento_Object $item)
    {
        $type = $this->_getItemType($item);
        $renderer = $this->_getRowItemRenderer($type)->setItem($item);
        $this->_prepareItem($renderer);
        return $renderer->toHtml();
    }

    /**
     * Retrieve renderer block for row-level item output
     *
     * @param string $type
     * @return Magento_Core_Block_Abstract
     */
    protected function _getRowItemRenderer($type)
    {
        $renderer = $this->getChildBlock($this->_getRowItemType($type));
        if ($renderer instanceof Magento_Core_Block) {
            $renderer->setRenderedBlock($this);
            return $renderer;
        }
        return parent::getItemRenderer($this->_getRowItemType(self::DEFAULT_TYPE));
    }

    /**
     * Wrap row renderers into namespace by adding 'row-' prefix
     *
     * @param string $type Product type
     * @return string
     */
    protected function _getRowItemType($type)
    {
        return 'row-' . $type;
    }
}
