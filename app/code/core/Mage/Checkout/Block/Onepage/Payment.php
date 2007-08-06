<?php
/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @subpackage Onepage
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Payment extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('payment', array('label'=>__('Payment Information')));
        parent::_construct();
    }
    
    public function getPayment()
    {
        $payment = $this->getQuote()->getPayment();
        if (empty($payment)) {
            $payment = Mage::getModel('sales/quote_entity_payment');
        } else {
            $payment->setCcNumber(null)->setCcCid(null);
        }
        return $payment;
    }
}