<?php
#include_once 'Ecom/Core/Module/Abstract.php';

class Ecom_Cart_Setup extends Ecom_Core_Setup_Abstract
{
    function load()
    {
        Ecom::addObserver('initLayout.after', array($this, 'updateLayout'));
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