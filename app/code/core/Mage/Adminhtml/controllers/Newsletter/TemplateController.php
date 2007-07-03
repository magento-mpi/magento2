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
class Mage_Adminhtml_Newsletter_TemplateController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('newsletter');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('newsletter'), __('newsletter title'), Mage::getUrl('adminhtml/newsletter'))
            ->addLink(__('newsletter templates'), __('newsletter templates title'));
        
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('adminhtml/newsletter_template', 'template'));
        $this->renderLayout();
    }
    
    public function newAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('newsletter');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('newsletter'), __('newsletter title'), Mage::getUrl('adminhtml/newsletter'))
            ->addLink(__('newsletter templates'), __('newsletter templates title'), Mage::getUrl('adminhtml/*'));
        
        if ($this->getRequest()->getParam('id')) {
            $this->getLayout()->getBlock('breadcrumbs')
                ->addLink(__('edit newsletter template'), __('edit newsletter template title'));
        } else {
            $this->getLayout()->getBlock('breadcrumbs')
                ->addLink(__('new newsletter template'), __('new newsletter template title'));
        }
        
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('adminhtml/newsletter_template_edit', 'template_edit')
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
                ->setTemplateType($request->getParam('type'))
                ->setTemplateText($request->getParam('text'));
            
            
            $template->save();
            $this->getResponse()->setRedirect(Mage::getUrl('adminhtml/*/'));
        }
        catch (Exception $e) {
            $this->_forward('new');
        }
        
    }
}
