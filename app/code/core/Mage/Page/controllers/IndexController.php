<?php


/**
 * Page Index Controller
 *
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @version    1.0
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Wed Feb 07 04:25:14 EET 2007
 */

class Mage_Page_IndexController extends Mage_Core_Controller_Front_Action
{
    function indexAction()
    {
        print_r($this->getLayout()->getNode());
    }

}// Class IndexController ENDclass