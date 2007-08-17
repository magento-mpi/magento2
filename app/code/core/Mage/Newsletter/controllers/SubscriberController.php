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
 	/**
 	 * Subscribe form 
 	 */
    public function indexAction() 
    {
        echo Mage::getSingleton('customer/session')->getWebsiteId();
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('newsletter/subscribe','subscribe.content');
        $this->getLayout()->getMessagesBlock()->setMessages(
        	Mage::getSingleton('newsletter/session')->getMessages(true)
        );
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
        
        
    }
    
    /**
 	 * New subscription action
 	 */
    public function newAction() 
    {
        $subscriber = Mage::getModel('newsletter/subscriber');
        $subscriber->loadByEmail($this->getRequest()->getParam('email'));
        $customerSession = Mage::getSingleton('customer/session');
        $session = Mage::getSingleton('newsletter/session');
        
        if(!$subscriber->getId()) {
           
            if($customerSession->isLoggedIn()) {
                $subscriber->setStoreId($customerSession->getCustomer()->getStoreId());
                $subscriber->setCustomerId($customerSession->getCustomerId());
                $subscriber->setSubscriberEmail($customerSession->getCustomer()->getEmail());
                $subscriber->setIsStatusChanged(true);
            } else {
                $subscriber->setSubscriberEmail($this->getRequest()->getParam('email'));
                $subscriber->setCustomerId(0);
                $subscriber->setStoreId(Mage::getSingleton('core/store')->getId());
            }
            
            try {
                $subscriber->save();
                
                if($subscriber->getCode()) {
                    $template = Mage::getModel('newsletter/template')->load(Mage::getStoreConfig('email/subscription_confirm'));
                    $template->send($subscriber, array('subscriber'=>$subscriber));
                }
                $session->addSuccess(__('You have been successfully subscribed'));
            }
            catch(Exception $e) {
                // Nothing
            }
        } else {
            $session->addSuccess(__('You have been successfully subscribed'));
        }
        
        $this->_redirect('*/*');
    }
    
    /**
     * Subscription confirm action
     */
    public function confirmAction() {
    	$id = (int) $this->getRequest()->getParam('id');
    	$subscriber = Mage::getModel('newsletter/subscriber')
    		->load($id);
    	
    	if($subscriber->getId() && $subscriber->getCode()) {
    		 if($subscriber->confirm($this->getRequest()->getParam('code'))) {
    		 	Mage::getSingleton('newsletter/session')->addSuccess('Your subscription successfully confirmed');	
    		 } else {
    		 	Mage::getSingleton('newsletter/session')->addError('Invalid subscription confirmation code');
    		 }
    	} else {
    		 Mage::getSingleton('newsletter/session')->addError('Invalid subscription id');
    	}
    	
    	$this->_redirect('*/*');
    }
 }