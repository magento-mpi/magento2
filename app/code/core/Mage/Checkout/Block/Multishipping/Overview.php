<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout overview information
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Multishipping_Overview extends Mage_Checkout_Block_Multishipping_Abstract
{
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(__('Review Order') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    public function getBillingAddress()
    {
        return $this->getCheckout()->getQuote()->getBillingAddress();
    }

    public function getPaymentHtml()
    {
        $payment = $this->getCheckout()->getQuote()->getPayment();
        $model = Mage::getStoreConfig('payment/'.$payment->getMethod().'/model');

        $block = Mage::getModel($model);
        if ($block) {
            $block->setPayment($payment)
                ->createInfoBlock($this->getName().'.payment');
        }

        $html = '<p>'.Mage::getStoreConfig('payment/'.$payment->getMethod().'/title').'</p>';
        $html .= $block->toHtml();

        return $html;
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
            $filter = Mage::app()->getStore()->getPriceFilter();
            $rate->setPrice($filter->filter($rate->getPrice()));
            return $rate;
        }
        return false;
    }

    public function getShippingAddressItems($address)
    {
        $priceFilter = Mage::app()->getStore()->getPriceFilter();
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        $itemsFilter->addFilter($priceFilter, 'price');
        $itemsFilter->addFilter($priceFilter, 'row_total');
        return $itemsFilter->filter($address->getAllItems());
    }

    public function getShippingAddressTotals($address)
    {
        $totals = $address->getTotals();
        foreach ($totals as $total) {
            if ($total->getCode()=='grand_total') {
                $total->setTitle(__('Total for this address'));
            }
        }
        $totalsFilter = new Varien_Filter_Object_Grid();
        $totalsFilter->addFilter(Mage::app()->getStore()->getPriceFilter(), 'value');
        return $totalsFilter->filter($totals);
    }

    public function getTotal()
    {
        $filter = Mage::app()->getStore()->getPriceFilter();
        return $filter->filter($this->getCheckout()->getQuote()->getGrandTotal());
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
}
