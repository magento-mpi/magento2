<?php
/**
 * One page checkout status
 *
 * @package    Mage
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Review extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('checkout/onepage/review/info.phtml');
        
        $checkout = Mage::getSingleton('checkout/session');
        $quote = $checkout->getQuote();
        $this->assign('checkout', $checkout)->assign('quote', $quote);
        
        $billing = $quote->getAddressByType('billing');
        if (empty($billing)) {
            $billing = Mage::getModel('sales/quote_entity_address');
        }
        $this->assign('billing', $billing);
        
        $payment = $quote->getPayment();
        if ($payment) {
            $paymentMethodConfig = Mage::getConfig()->getNode('global/sales/payment/methods/'.$payment->getMethod());
            if (!empty($paymentMethodConfig)) {
                $className = $paymentMethodConfig->getClassName();
                $paymentMethod = new $className();
                $paymentBlock = $paymentMethod->setPayment($payment)->createInfoBlock($this->getData('name').'.payment');
                $this->setChild('payment', $paymentBlock);
            } else {
                $this->assign('payment', '');
            }
        } else {
            $this->assign('payment', '');
        }
        
        $shipping = $quote->getAddressByType('shipping');
        if (empty($shipping)) {
            $shipping = Mage::getModel('sales/quote_entity_address');
        }
        $this->assign('shipping', $shipping);
        
        $this->assign('shippingDescription', $quote->getShippingDescription());
    
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'price');
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'row_total');
        $this->assign('items', $itemsFilter->filter($quote->getItems()));

        $totalsFilter = new Varien_Filter_Array_Grid();
        $totalsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'value');
        $this->assign('totals', $totalsFilter->filter($quote->getTotals()));
    }
}