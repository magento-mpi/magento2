<?php

class Mage_Payment_Block_Info_Cc extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('payment/info/ccsave.phtml');
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
    
    public function getCcTypeName($type)
    {
    	$types = $this->getCcTypes();
    	return isset($types[$type]) ? $types[$type] : $type;
    }
}