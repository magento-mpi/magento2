<?php

class Mage_Sales_Block_Payment_Cc_Info extends Mage_Core_Block_Text
{
    public function init(Varien_Data_Object $payment)
    {
        $out = __('Credit Card')."\n".
            __('Type').': '.$payment->getCcType()."\n".
            __('Owner').': '.$payment->getCcOwner()."\n".
            __('Number').': '.str_pad('', 4, 'x').$payment->getCcLast4()."\n".
            __('Expiration').': '.$payment->getCcExpires();
            
        $this->setText(nl2br($out));
        
        return $this;
    }
}