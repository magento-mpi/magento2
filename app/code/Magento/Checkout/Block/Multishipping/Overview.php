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
namespace Magento\Checkout\Block\Multishipping;

class Overview extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * Initialize default item renderer
     */
    protected function _prepareLayout()
    {
        $rowItemType = $this->_getRowItemType(self::DEFAULT_TYPE);
        if (!$this->getChildBlock($rowItemType)) {
            $this->addChild(
                $rowItemType,
                'Magento\Checkout\Block\Cart\Item\Renderer',
                array('template' => 'multishipping/overview/item.phtml')
            );
        }
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(
                __('Review Order - %1', $headBlock->getDefaultTitle())
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Get multishipping checkout model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping
     */
    public function getCheckout()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Type\Multishipping');
    }

    public function getBillingAddress()
    {
        return $this->getCheckout()->getQuote()->getBillingAddress();
    }

    public function getPaymentHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Get object with payment info posted data
     *
     * @return \Magento\Object
     */
    public function getPayment()
    {
        if (!$this->hasData('payment')) {
            $payment = new \Magento\Object($this->getRequest()->getPost('payment'));
            $this->setData('payment', $payment);
        }
        return $this->_getData('payment');
    }

    public function getShippingAddresses()
    {
        return $this->getCheckout()->getQuote()->getAllShippingAddresses();
    }

    public function getShippingAddressCount()
    {
        $count = $this->getData('shipping_address_count');
        if (is_null($count)) {
            $count = count($this->getShippingAddresses());
            $this->setData('shipping_address_count', $count);
        }
        return $count;
    }

    public function getShippingAddressRate($address)
    {
        if ($rate = $address->getShippingRateByCode($address->getShippingMethod())) {
            return $rate;
        }
        return false;
    }

    public function getShippingPriceInclTax($address)
    {
        $exclTax = $address->getShippingAmount();
        $taxAmount = $address->getShippingTaxAmount();
        return $this->formatPrice($exclTax + $taxAmount);
    }

    public function getShippingPriceExclTax($address)
    {
        return $this->formatPrice($address->getShippingAmount());
    }

    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }

    public function getShippingAddressItems($address)
    {
        return $address->getAllVisibleItems();
    }

    public function getShippingAddressTotals($address)
    {
        $totals = $address->getTotals();
        foreach ($totals as $total) {
            if ($total->getCode()=='grand_total') {
                if ($address->getAddressType() == \Magento\Sales\Model\Quote\Address::TYPE_BILLING) {
                    $total->setTitle(__('Total'));
                }
                else {
                    $total->setTitle(__('Total for this address'));
                }
            }
        }
        return $totals;
    }

    public function getTotal()
    {
        return $this->getCheckout()->getQuote()->getGrandTotal();
    }

    public function getAddressesEditUrl()
    {
        return $this->getUrl('*/*/backtoaddresses');
    }

    public function getEditShippingAddressUrl($address)
    {
        return $this->getUrl('*/multishipping_address/editShipping', array('id'=>$address->getCustomerAddressId()));
    }

    public function getEditBillingAddressUrl($address)
    {
        return $this->getUrl('*/multishipping_address/editBilling', array('id'=>$address->getCustomerAddressId()));
    }

    public function getEditShippingUrl()
    {
        return $this->getUrl('*/*/backtoshipping');
    }

    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/overviewPost');
    }

    public function getEditBillingUrl()
    {
        return $this->getUrl('*/*/backtobilling');
    }

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
     * @return \Magento\Sales\Model\Qoute
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function getBillinAddressTotals()
    {
        $_address = $this->getQuote()->getBillingAddress();
        return $this->getShippingAddressTotals($_address);
    }


    public function renderTotals($totals, $colspan=null)
    {
        if ($colspan === null) {
            $colspan = $this->helper('Magento\Tax\Helper\Data')->displayCartBothPrices() ? 5 : 3;
        }
        $totals = $this->getChildBlock('totals')->setTotals($totals)->renderTotals('', $colspan)
            . $this->getChildBlock('totals')->setTotals($totals)->renderTotals('footer', $colspan);
        return $totals;
    }

    /**
     * Return row-level item html
     *
     * @param \Magento\Object $item
     * @return string
     */
    public function getRowItemHtml(\Magento\Object $item)
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
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _getRowItemRenderer($type)
    {
        $renderer = $this->getChildBlock($this->_getRowItemType($type));
        if ($renderer instanceof \Magento\Core\Block) {
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
