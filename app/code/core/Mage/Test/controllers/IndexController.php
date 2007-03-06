<?php

/**
 * Page Index Controller
 *
 * @copyright  Varien, 2007
 * @version    1.0
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Wed Feb 07 04:25:14 EET 2007
 */

class Mage_Test_IndexController extends Mage_Core_Controller_Front_Action
{
    function indexAction() 
    {
        Mage::getBlock('content')->append(Mage::createBlock('text')->setText('<h1>My test content</h1>'));
    }

}// Class IndexController END