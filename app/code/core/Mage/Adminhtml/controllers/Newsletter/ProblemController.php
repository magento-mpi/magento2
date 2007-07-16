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
		
		$this->_setActiveMenu('newsletter/subscriber');
		
		$this->_addBreadcrumb(__('Newsletter'), __('newsletter title'), Mage::getUrl('adminhtml/newsletter'));
		$this->_addBreadcrumb(__('Problem'), __('Problem title'));		
		
		$this->_addContent(
			$this->getLayout()->createBlock('adminhtml/newsletter_problem','problem')
		);
		
		$this->renderLayout();	
	}	
	 
	public function gridAction()
    {
    	$this->getLayout()->getMessagesBlock()->setMessages(Mage::getSingleton('adminhtml/session')->getMessages(true));
    	$grid = $this->getLayout()->createBlock('adminhtml/newsletter_problem');
    	$this->getResponse()->setBody($grid->toHtml());
    }
}// Class Mage_Adminhtml_Newsletter_ProblemController END