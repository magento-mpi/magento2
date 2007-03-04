<?php
#include_once 'Ecom/Core/Module/Abstract.php';

class Ecom_Acl_Module extends Ecom_Core_Module_Abstract
{
    protected $_info = array(
        'name'=>'Ecom_Acl',
        'version'=>'0.1.0a1',
    );

    function load()
    {

    }
    
    function run()
    {
        Ecom::dispatchEvent(__METHOD__);
    }
}