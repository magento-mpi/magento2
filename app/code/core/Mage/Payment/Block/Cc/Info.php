<?php

class Mage_Payment_Block_Cc_Info extends Mage_Core_Block_Text
{
    public function toHtml()
    {
		if (!$this->_beforeToHtml()) {
			return '';
		}

        $payment = $this->getPayment();
        $out = __('Credit Card')."\n".
            __('Type').': '.$payment->getCcType()."\n".
            __('Owner').': '.$payment->getCcOwner()."\n".
            __('Number').': '.str_pad('', 4, 'x').$payment->getCcLast4()."\n".
            __('Expiration').': '.sprintf("%02d/%04d", $payment->getCcExpMonth(), $payment->getCcExpYear());
            
        $this->setText(nl2br($out));
        
        return parent::toHtml();
    }
}