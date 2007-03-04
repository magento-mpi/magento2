<?php
#include_once 'Ecom/Core/Module/Abstract.php';

class Ecom_Sales_Module extends Ecom_Core_Module_Abstract
{
    protected $_info = array(
        'name'=>'Ecom_Sales',
        'version'=>'0.1.0a4',
    );

    function load()
    {

    }
    
    function run()
    {
        Ecom::dispatchEvent(__METHOD__);
    }
}