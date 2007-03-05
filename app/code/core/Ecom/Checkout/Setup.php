<?php
#include_once 'Ecom/Core/Module/Abstract.php';

class Ecom_Checkout_Setup extends Ecom_Core_Setup_Abstract
{
    function loadFront()
    {
        Ecom::addObserver('initLayout.after', array($this, 'updateLayout'));
    }

    function updateLayout()
    {
        $moduleBaseUrl = $this->getModuleInfo()->getBaseUrl();

        $updateLayout = array(':checkout.layout.update',
            array('#top.links', array('>append', array('+list_link', '#.checkout', array('>setLink', '', 'href="'.$moduleBaseUrl.'" title="Checkout"', 'Checkout')))),
        );
        Ecom_Core_Block::loadArray($updateLayout);
    }
}