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

class Ecom_Page_IndexController extends Ecom_Core_Controller_Action
{
    function indexAction()
    {
        #Ecom::getBlock('root')->setGroup('layout.3column', -1);
        #Ecom::getModel('core', 'Block')->saveGroup('layout.3column');
        $blocks = Ecom_Core_Block::getAllBlocks();
        $debug = Ecom::createBlock('debug')->setValue(array_keys($blocks));
        Ecom::getBlock('content')->append($debug);
    }

}// Class IndexController ENDclass