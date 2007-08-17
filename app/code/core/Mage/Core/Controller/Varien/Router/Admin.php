<?php

class Mage_Core_Controller_Varien_Router_Admin extends Mage_Core_Controller_Varien_Router_Standard 
{
    public function fetchDefault()
    {
        Mage::getSingleton('core/store')->load(0);
        Mage::getSingleton('core/website')->load(0);
        
    	// set defaults
        $d = explode('/', Mage::getStoreConfig('web/default/admin'));
        $this->getFront()->setDefault(array(
            'module'     => !empty($d[0]) ? $d[0] : '', 
            'controller' => !empty($d[1]) ? $d[1] : 'index', 
            'action'     => !empty($d[2]) ? $d[2] : 'index'
        ));
    }

}