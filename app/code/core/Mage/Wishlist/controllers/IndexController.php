<?php
/**
 * Wishlist controller
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Wishlist_IndexController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        
        $action = $this->getRequest()->getActionName();
        if (!preg_match('#^(create|login|forgotpassword)#', $action)) {
            if (!Mage::getSingleton('customer', 'session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }
    
    /**
     * Default account page
     *
     */
    public function indexAction() 
    {
        $this->loadLayout();
        
        $block = Mage::createBlock('tpl', 'wishlist')
            ->setTemplate('wishlist/view.phtml');
            //->assign('messages',    Mage::getSingleton('customer', 'session')->getMessages(true))
            //->assign('customer', Mage::getSingleton('customer', 'session')->getCustomer());
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
}// Class Mage_Wishlist_IndexController END