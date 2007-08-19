<?php 
/**
 * Adminhtml newsletter subscribers controller
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Newsletter_SubscriberController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() 
	{
	    if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        $this->getLayout()->getMessagesBlock()->setMessages(Mage::getSingleton('adminhtml/session')->getMessages(true));
		$this->loadLayout('baseframe');
		
		$this->_setActiveMenu('newsletter/subscriber');
		
		$this->_addBreadcrumb(__('Newsletter'), __('Newsletter'));
		$this->_addBreadcrumb(__('Subscribers'), __('Subscribers'));
		
		$this->_addContent(
			$this->getLayout()->createBlock('adminhtml/newsletter_subscriber','subscriber')
		);
		
		$this->renderLayout();	
	}	
	
	public function gridAction()
    {
    	if($this->getRequest()->getParam('add') == 'subscribers') {
    		try {
	    		Mage::getModel('newsletter/queue')
	    			->load($this->getRequest()->getParam('queue'))
	    			->addSubscribersToQueue($this->getRequest()->getParam('subscriber', array()));
	    		Mage::getSingleton('adminhtml/session')->addSuccess('Selected subscribers successfully added to selected queue');
    		} 
    		catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    		}
    	}
    	
    	$this->getLayout()->getMessagesBlock()->setMessages(Mage::getSingleton('adminhtml/session')->getMessages(true));
    	$grid = $this->getLayout()->createBlock('adminhtml/newsletter_subscriber_grid');
    	$this->getResponse()->setBody($grid->toHtml());
    }
}// Class Mage_Adminhtml_Newsletter_SubscriberController END
