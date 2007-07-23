<?php
/**
 * Adminhtml newsletter queue controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Newsletter_QueueController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Queue list action
     */
    public function indexAction()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        $this->loadLayout('baseframe');

        $this->_setActiveMenu('newsletter/queue');
        
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/newsletter_queue', 'queue')
        );
       
        $this->_addBreadcrumb(__('Newsletter Queue'), __('Newsletter Queue'));

        $this->renderLayout();
    }

    /**
     * Queue list Ajax action
     */
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/newsletter_queue_grid')->toHtml());
    }
	
    public function startAction() 
    {
    	$queue = Mage::getModel('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));
    	if ($queue->getId()) {
    		if(!in_array($queue->getQueueStatus(),
	    		 		 array(Mage_Newsletter_Model_Queue::STATUS_NEVER,
	    		 		 	   Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {
	   			$this->_redirect('*/*');
	    		return;
	    	}
	    	
    		$queue->setQueueStartAt(now())
    			->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING)
    			->save();
    	}
    	
    	$this->_redirect('*/*');
    }
    
    public function pauseAction()
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));
    	
    	if(!in_array($queue->getQueueStatus(),
    		 		 array(Mage_Newsletter_Model_Queue::STATUS_SENDING))) {
   			$this->_redirect('*/*');
    		return;
    	}
    	
    	$queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_PAUSE);
    	$queue->save();
    	
    	$this->_redirect('*/*');
    }
    
    public function resumeAction()
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));
    	
    	if(!in_array($queue->getQueueStatus(),
    		 		 array(Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {
   			$this->_redirect('*/*');
    		return;
    	}
    	
    	$queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING);
    	$queue->save();
    	
    	$this->_redirect('*/*');
    }
    
    public function cancelAction()
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));
    	
    	if(!in_array($queue->getQueueStatus(),
    		 		 array(Mage_Newsletter_Model_Queue::STATUS_SENDING))) {
   			$this->_redirect('*/*');
    		return;
    	}
    	
    	$queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_CANCEL);
    	$queue->save();
    	
    	$this->_redirect('*/*');
    }
    
    public function sendingAction()
    {
    	// Todo: put it somewhere in config!
    	$countOfQueue  = 3;
    	$countOfSubscritions = 20;
    	
    	$collection = Mage::getResourceModel('newsletter/queue_collection')
    		->setPageSize($countOfQueue)
    		->setCurPage(1)
    		->addOnlyForSendingFilter()
    		->load();
    		
    	$collection->walk('sendPerSubscriber', array($countOfSubscritions));
    }
    
    
    public function editAction() 
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));
    	
    	if(!in_array($queue->getQueueStatus(),
    		 		 array(Mage_Newsletter_Model_Queue::STATUS_NEVER,
    		 		 	   Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {
   			$this->_redirect('*/*');
    		return;
    	}
    	
    	$this->loadLayout('baseframe');
    	
    	$this->_setActiveMenu('newsletter/queue');
    	    	
        $this->_addBreadcrumb(__('Newsletter Queue'), __('Newsletter Queue'), Mage::getUrl('adminhtml/newsletter_queue'));
        $this->_addBreadcrumb(__('Edit Queue'), __('Edit Queue Title'));
        
        $this->_addContent(
        	$this->getLayout()->createBlock('adminhtml/newsletter_queue_edit', 'queue.edit')
        );
    	
    	$this->renderLayout();
    }
    
    public function saveAction()
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));
    	
    	if(!in_array($queue->getQueueStatus(),
    		 		 array(Mage_Newsletter_Model_Queue::STATUS_NEVER,
    		 		 	   Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {
   			$this->_redirect('*/*');
    		return;
    	}
    	
    	if($this->getRequest()->getParam('start_at') && $queue->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
    		$queue->setQueueStartAt(
    			date('Y-m-d H:i:s', strtotime($this->getRequest()->getParam('start_at')))
    		);
    	} 
    	
    	$queue->addTemplateData($queue);
    	$queue->getTemplate()
    		->setTemplateSubject($this->getRequest()->getParam('subject'))
    		->setTemplateSenderName($this->getRequest()->getParam('sender_name'))
    		->setTemplateSenderEmail($this->getRequest()->getParam('sender_email'))
    		->setTemplateTextPreprocessed($this->getRequest()->getParam('text'));
    	$queue->setSaveTemplateFlag(true);
    	$queue->save();
    	
    	$this->_redirect('*/*');
    }
}
