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

class Mage_Adminhtml_Newsletter_ProblemController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() 
	{
	    if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        $this->getLayout()->getMessagesBlock()->setMessages(Mage::getSingleton('adminhtml/session')->getMessages(true));
        $this->loadLayout('baseframe');
		
		$this->_setActiveMenu('newsletter/problem');
		
		
		$this->_addBreadcrumb(__('Newsletter Problem Reports'), __('Newsletter Problem Reports'));		
		
		$this->_addContent(
			$this->getLayout()->createBlock('adminhtml/newsletter_problem','problem')
		);
		
		$this->renderLayout();	
	}	
	 
	public function gridAction()
    {
    	if($this->getRequest()->getParam('_unsubscribe')) {
    		$problems = (array) $this->getRequest()->getParam('problem', array());
    		if (count($problems)>0) {
    			$collection = Mage::getResourceModel('newsletter/problem_collection');
    			$collection
    				->addSubscriberInfo()
    				->addFieldToFilter($collection->getResource()->getIdFieldName(), 
    								   array('in'=>$problems))
    				->load();
    			
    			$collection->walk('unsubscribe');
    		}
    		
    		Mage::getSingleton('adminhtml/session')
    			->addSuccess('Selected problem subscribers successfully unsubscribed');
    	} 
    	
    	if($this->getRequest()->getParam('_delete')) {
    		$problems = (array) $this->getRequest()->getParam('problem', array());
    		if (count($problems)>0) {
    			$collection = Mage::getResourceModel('newsletter/problem_collection');
    			$collection
    				->addFieldToFilter($collection->getResource()->getIdFieldName(), 
    								   array('in'=>$problems))
    				->load();
    			$collection->walk('delete');
    		}
    		
    		Mage::getSingleton('adminhtml/session')
    			->addSuccess('Selected problems successfully deleted');
    	} 
    	    	$this->getLayout()->getMessagesBlock()->setMessages(Mage::getSingleton('adminhtml/session')->getMessages(true));
    	
    	$grid = $this->getLayout()->createBlock('adminhtml/newsletter_problem_grid');
    	$this->getResponse()->setBody($grid->toHtml());
    }
}// Class Mage_Adminhtml_Newsletter_ProblemController END
