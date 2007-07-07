<?php 
/**
 * Newsletter subscribe controller 
 *
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 
 class Mage_Newsletter_SubscriberController extends Mage_Core_Controller_Front_Action 
 {
    public function indexAction() 
    {
        echo Mage::getSingleton('customer/session')->getWebsiteId();
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('newsletter/subscribe','subscribe.content');
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
        
        
    }
    
    public function newAction() 
    {
        $subscriber = Mage::getModel('newsletter/subscriber');
        $subscriber->loadByEmail($this->getRequest()->getParam('email'));
        $customerSession = Mage::getSingleton('customer/session');
        $session = Mage::getSingleton('newsletter/session');
        
        if(!$subscriber->getId()) {
            // TODO: BIND TO STORE ID!
            if($customerSession->isLoggedIn()) {
                
                $subscriber->setCustomerId($customerSession->getCustomerId());
                $subscriber->setSubscriberEmail($customerSession->getCustomer()->getEmail());
            } else {
                $subscriber->setSubscriberEmail($this->getRequest()->getParam('email'));
                $subscriber->setCustomerId(0);
            }
            
            try {
                $subscriber->save();
                
                if($subscriber->getCode()) {
                    
                    $template = Mage::getModel('newsletter/template')->loadByCode('subscriberCodeConfirm');
                    $template->send($subscriber, array('subscriber'=>$subscriber));
                    
                }
                $session->addMessage(Mage::getModel('newsletter/message')->success('You successfully subscribed'));
            }
            catch(Exception $e) {
                
                echo $e->getMessage();
                exit();
            }
        } else {
            $session->addMessage(Mage::getModel('newsletter/message')->success('You successfully subscribed');
        }
        
        $this->_redirect('*/*');
    }
 }