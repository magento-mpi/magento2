<?php


/**
 * Page Index Controller
 *
 * @copyright  Varien, 2007
 * @version    1.0
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Wed Feb 07 04:25:14 EET 2007
 */

class Mage_Page_IndexController extends Mage_Core_Controller_Front_Action
{
    function indexAction()
    {
        #Mage::getBlock('root')->setGroup('layout.3column', -1);
        #Mage::getResourceModel('core', 'Block')->saveGroup('layout.3column');
        #$blocks = Mage::registry('blocks')->getAllBlocks();
        #$debug = Mage::createBlock('debug')->setValue(array_keys($blocks));
        #Mage::getBlock('content')->append($debug);
        print_r($this->getLayout()->getXml());
    }

}// Class IndexController ENDclass