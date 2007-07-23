<?php
/**
 * Newsletter admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Newsletter_TemplateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
    	$this->loadLayout('baseframe');
        $this->_setActiveMenu('newsletter/template');
       $this->_addBreadcrumb(__('Newsletter Templates'), __('Newsletter Templates Title'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/newsletter_template', 'template'));
        $this->renderLayout();
    }
    
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/newsletter_template_grid')->toHtml());
    }
    

    public function newAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('newsletter/template');
        $this->_addBreadcrumb(__('Newsletter Templates'), __('Newsletter Templates Title'), Mage::getUrl('adminhtml/*'));

        if ($this->getRequest()->getParam('id')) {
            $this->_addBreadcrumb(__('Edit Template'), __('Edit Newsletter Template Title'));
        } else {
            $this->_addBreadcrumb(__('New Template'), __('Create Newsletter Template Title'));
        }

        $this->_addContent($this->getLayout()->createBlock('adminhtml/newsletter_template_edit', 'template_edit')
                                                            ->setEditMode((bool)$this->getRequest()->getParam('id')));
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_forward('new');
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        $template = Mage::getModel('newsletter/template');
        if ($id = (int)$request->getParam('id')) {
            $template->load($id);
        }

        try {
            $template->setTemplateSubject($request->getParam('subject'))
                ->setTemplateCode($request->getParam('code'))
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
                ->setTemplateText($request->getParam('text'))
                ->setModifiedAt(now());

            if (!$template->getId()) {
                $type = constant(Mage::getConfig()->getModelClassName('newsletter/template') . "::TYPE_HTML");
                $template->setTemplateType($type);
            }

            if($this->getRequest()->getParam('_change_type_flag')) {
                $type = constant(Mage::getConfig()->getModelClassName('newsletter/template') . "::TYPE_TEXT");
                $template->setTemplateType($type);
            }

            $template->save();
            $this->_redirect('*/*');
        }
        catch (Exception $e) {
        	Mage::getSingleton('adminhtml/session')->setData('newsletter_template_form_data', $this->getRequest()->getParams());
        	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        	$this->_forward('new');
        }

    }

    public function deleteAction() 
    {

        $template = Mage::getModel('newsletter/template');
        $id = (int)$this->getRequest()->getParam('id');
        $template->load($id);
        if($template->getId()) {
            try {
                $template->delete();
            }
            catch (Exception $e) {
                // Nothing
            }
        }
        $this->_redirect('*/*');
    }

    public function previewAction()
    {
        $this->loadLayout('preview');
        $this->renderLayout();
    }
    
    public function toqueueAction()
    {
    	$template = Mage::getModel('newsletter/template')
        	->load($this->getRequest()->getParam('id'));
        
        if(!$template->getIsSystem()) {
            $template->preprocess();
			
			$queue = Mage::getModel('newsletter/queue')
				->setTemplateId($this->getRequest()->getParam('id'))
		    	->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_NEVER);
		    
		    $queue->save();
		    $template->save();
		    
		    $this->_redirect('*/newsletter_queue/edit', array('id'=>$queue->getId()));
        }
    }
}
