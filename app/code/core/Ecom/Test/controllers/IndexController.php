<?php

#include_once 'Ecom/Core/Controller/Zend/Action.php';
/**
 * Page Index Controller
 *
 * @copyright  Varien, 2007
 * @version    1.0
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Wed Feb 07 04:25:14 EET 2007
 */

class Ecom_Test_IndexController extends Ecom_Core_Controller_Action
{
    function indexAction() 
    {
        Ecom::getBlock('content')->append(Ecom::createBlock('text')->setText('<h1>My test content</h1>'));
    }

}// Class IndexController END