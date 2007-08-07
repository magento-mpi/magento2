<?php

class Mage_Payment_Block_Form_Cc extends Mage_Payment_Block_Form 
{
    protected function _construct()
    {
        $this->setTemplate('payment/form/ccsave.phtml');
        parent::_construct();
    }
    
    public function getCcTypes()
    {
        return array(
            ''=>__('Please select credit card type'),
            'AE'=>__('American Express'),
            'VI'=>__('Visa'),
            'MC'=>__('Master Card'),
            'DI'=>__('Discover'),
        );
    }
    
    public function getMonths()
    {
        return array(
            ''=>__('Month'),
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
    }
    
    public function getYears()
    {
        for ($yearsArr=array(''=>__('Year')), $y1=date("Y"), $y=0; $y<10; $y++) {
            $yearsArr[$y1+$y] = $y1+$y;
        }
        return $yearsArr;
    }
}