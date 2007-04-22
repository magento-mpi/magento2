<?php

class Mage_Sales_Block_Payment_Cc_Form extends Mage_Core_Block_Template 
{
    public function init(Varien_Data_Object $payment)
    {
        $ccTypesArr = array(
            'AE'=>'American Express',
            'VI'=>'Visa',
            'MC'=>'Master Card',
            'DI'=>'Discover',
        );

        $monthsArr = array(
             1=>'01-'.__('January'),
             2=>'02-'.__('February'),
             3=>'03-'.__('March'),
             4=>'04-'.__('April'),
             5=>'05-'.__('May'),
             6=>'06-'.__('June'),
             7=>'07-'.__('July'),
             8=>'08-'.__('August'),
             9=>'09-'.__('September'),
            10=>'10-'.__('October'),
            11=>'11-'.__('November'),
            12=>'12-'.__('December'),
        );
        
        for ($yearsArr=array(), $y1=date("Y"), $y=0; $y<10; $y++) {
            $yearsArr[$y1+$y] = $y1+$y;
        }

        $this->setTemplate('sales/payment/ccsave.phtml')
            ->assign('ccTypesArr', $ccTypesArr)
            ->assign('monthsArr', $monthsArr)
            ->assign('yearsArr', $yearsArr)
            ->assign('payment', $payment);
        
        return $this;
    }
}