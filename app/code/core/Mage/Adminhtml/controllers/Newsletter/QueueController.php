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

        $this->_addBreadcrumb(__('Newsletter'), __('newsletter title'), Mage::getUrl('adminhtml/newsletter'));
        $this->_addBreadcrumb(__('Queue'), __('Queue title'));

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
    		$queue->setQueueStartAt(now())
    			->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDIND)
    			->save();
    	}
    	
    	$this->_redirect('*/*');
    }
    
    public function editAction() 
    {
    	$this->loadLayout('baseframe');
    	
    	$this->_setActiveMenu('newsletter/queue');
    	
    	$this->_addBreadcrumb(__('Newsletter'), __('newsletter title'), Mage::getUrl('adminhtml/newsletter'));
        $this->_addBreadcrumb(__('Queue'), __('Queue title'), Mage::getUrl('adminhtml/newsletter_queue'));
        $this->_addBreadcrumb(__('Edit queue'), __('Edit queue title'));
        
        $this->_addContent(
        	$this->getLayout()->createBlock('adminhtml/newsletter_queue_edit', 'queue.edit')
        );
    	
    	$this->renderLayout();
    }
}