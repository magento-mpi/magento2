<?php
/**
 * One page checkout status
 *
 * @package    Mage
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Review_Info extends Mage_Checkout_Block_Onepage_Abstract
{    
    public function getBilling()
    {
        return $this->getQuote()->getBillingAddress();
    }
    
    public function getShipping()
    {
        return $this->getQuote()->getShippingAddress();
    }
    
    public function getPaymentBlock()
    {
        $payment = $this->getQuote()->getPayment();
        $paymentMethodConfig = Mage::getConfig()->getNode('global/sales/payment/methods/'.$payment->getMethod());
        if (!empty($paymentMethodConfig)) {
            $className = $paymentMethodConfig->getClassName();
            $paymentMethod = new $className();
            $paymentBlock = $paymentMethod->setPayment($payment)->createInfoBlock($this->getData('name').'.payment');
        } else {
            $paymentBlock = $this->getLayout()->createBlock('core/text');
        }
        return $paymentBlock;
    }
    
    public function getShippingDescription()
    {
        return $this->getQuote()->getShippingAddress()->getShippingDescription();
    }
    
    public function getItems()
    {
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'price');
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'row_total');
        $this->assign('items', $itemsFilter->filter($this->getQuote()->getItems()));
    }
    
    public function getTotals()
    {
        $totalsFilter = new Varien_Filter_Array_Grid();
        $totalsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'value');
        $this->assign('totals', $totalsFilter->filter($this->getQuote()->getTotals()));
    }
}