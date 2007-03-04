<?php
#include_once 'Ecom/Core/Module/Abstract.php';

class Ecom_Cart_Module extends Ecom_Core_Module_Abstract
{
    protected $_info = array(
        'name'=>'Ecom_Cart',
        'version'=>'0.1.0a1',
    );

    function load()
    {
        Ecom::addObserver('initLayout.after', array($this, 'updateLayout'));
    }
    
    function run()
    {
        Ecom::dispatchEvent(__METHOD__);
    }
    
    function updateLayout()
    {
        $moduleBaseUrl = $this->getModuleInfo()->getBaseUrl();
        
        $updateLayout = array(':cart.layout.update',
            array('#top.links', array('>append', array('+list_link', '#.mycart', array('>setLink', '', 'href="'.$moduleBaseUrl.'"', 'My Cart')))),
        );

        Ecom_Core_Block::loadArray($updateLayout);
    }
}