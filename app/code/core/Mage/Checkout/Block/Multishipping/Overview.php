<?php
/**
 * Multishipping checkout overview information
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Multishipping_Overview extends Mage_Checkout_Block_Multishipping_Abstract
{
    public function getBillingAddress()
    {
        return $this->getCheckout()->getQuote()->getBillingAddress();
    }

    public function getPaymentHtml()
    {
        $payment = $this->getCheckout()->getQuote()->getPayment();
        $model = Mage::getStoreConfig('payment/'.$payment->getMethod().'/model');

        $block = Mage::getModel($model)
            ->setPayment($payment)
            ->createInfoBlock($this->getData('name').'.payment');

        return $block->getTitle();
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
            $filter = Mage::getSingleton('core/store')->getPriceFilter();
            $rate->setPrice($filter->filter($rate->getPrice()));
            return $rate;
        }
        return false;
    }

    public function getShippingAddressItems($address)
    {
		$priceFilter = Mage::getSingleton('core/store')->getPriceFilter();
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        $itemsFilter->addFilter($priceFilter, 'price');
        $itemsFilter->addFilter($priceFilter, 'row_total');
        return $itemsFilter->filter($address->getAllItems());
    }

    public function getShippingAddressTotals($address)
    {
        $totalsFilter = new Varien_Filter_Object_Grid();
        $totalsFilter->addFilter(Mage::getSingleton('core/store')->getPriceFilter(), 'value');
        return $totalsFilter->filter($address->getTotals());
    }

    public function getTotal()
    {
        $filter = Mage::getSingleton('core/store')->getPriceFilter();
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
