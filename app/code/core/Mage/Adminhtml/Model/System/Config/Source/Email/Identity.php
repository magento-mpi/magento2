<?php

class Mage_Adminhtml_Model_System_Config_Source_Email_Identity
{
    public function toOptionArray()
    {
       	$identities = Mage::getResourceModel('core/config_field_collection')
       		->addFieldToFilter("level", 2)
       		->addFieldToFilter("path", array('like'=>'trans_email/ident_%'))
       		->load();
       		
        $arr = array();
       	foreach ($identities as $ident) {
			$arr[] = array(
				'value' => preg_replace('#^trans_email/ident_(.*)$#', '$1', $ident->getPath()),
				'label' => $ident->getFrontendLabel(),
			);
       	}
        
        return $arr;
    }
}