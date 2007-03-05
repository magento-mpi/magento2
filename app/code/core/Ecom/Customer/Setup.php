<?php

#include_once 'Ecom/Core/Module/Abstract.php';

class Ecom_Customer_Setup extends Ecom_Core_Setup_Abstract
{
    function loadFront()
    {
        Ecom::addObserver('initLayout.after', array($this, 'updateLayout'));
    }
    
    function updateLayout()
    {
        $moduleBaseUrl = $this->getModuleInfo()->getBaseUrl();
      
        $updateLayout = array(':customer.layout.update',
            array('#head', array('>append', array('+tag_css', array('>setHref', '/customer/style.css')))),
            array('#left', array('>append', array('+tpl', '#.newsletter', array('>setViewName', 'Ecom_Customer', 'newsletter.form.mini')))),
            array('#top.links', 
                array('>append', array('+list_link', '#.myaccount', array('>setLink', '', 'href="'.$moduleBaseUrl.'/account" title="My Account"', 'My Account'))),
                array('>append', array('+list_link', '#.favorite', array('>setLink', '', 'href="'.$moduleBaseUrl.'/favorite" title="Favorite"', 'Favorite'))),
            )
        );
        Ecom_Core_Block::loadArray($updateLayout);
    }
}