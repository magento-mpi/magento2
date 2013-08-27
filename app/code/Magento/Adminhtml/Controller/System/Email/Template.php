<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Template admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_System_Email_Template extends Magento_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title(__('Email Templates'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Adminhtml::system_email_template');
        $this->_addBreadcrumb(__('Transactional Emails'), __('Transactional Emails'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }


    /**
     * New transactional email action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit transactioanl email action
     *
     */
    public function editAction()
    {
        $this->loadLayout();
        $template = $this->_initTemplate('id');
        $this->_setActiveMenu('Magento_Adminhtml::system_email_template');
        $this->_addBreadcrumb(__('Transactional Emails'), __('Transactional Emails'), $this->getUrl('*/*'));

        if ($this->getRequest()->getParam('id')) {
            $this->_addBreadcrumb(__('Edit Template'), __('Edit System Template'));
        } else {
            $this->_addBreadcrumb(__('New Template'), __('New System Template'));
        }

        $this->_title($template->getId() ? $template->getTemplateCode() : __('New Template'));

        $this->_addContent($this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_System_Email_Template_Edit', 'template_edit')
            ->setEditMode((bool)$this->getRequest()->getParam('id'))
        );
        $this->renderLayout();
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        $id = $this->getRequest()->getParam('id');

        $template = $this->_initTemplate('id');
        if (!$template->getId() && $id) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('This email template no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $template->setTemplateSubject($request->getParam('template_subject'))
                ->setTemplateCode($request->getParam('template_code'))
/*
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
*/
                ->setTemplateText($request->getParam('template_text'))
                ->setTemplateStyles($request->getParam('template_styles'))
                ->setModifiedAt(Mage::getSingleton('Magento_Core_Model_Date')->gmtDate())
                ->setOrigTemplateCode($request->getParam('orig_template_code'))
                ->setOrigTemplateVariables($request->getParam('orig_template_variables'));

            if (!$template->getId()) {
                $template->setTemplateType(Magento_Core_Model_Email_Template::TYPE_HTML);
            }

            if($request->getParam('_change_type_flag')) {
                $template->setTemplateType(Magento_Core_Model_Email_Template::TYPE_TEXT);
                $template->setTemplateStyles('');
            }

            $template->save();
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData(false);
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('The email template has been saved.'));
            $this->_redirect('*/*');
        }
        catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->setData('email_template_form_data', $this->getRequest()->getParams());
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            $this->_forward('new');
        }

    }

    public function deleteAction() {

        $template = $this->_initTemplate('id');
        if($template->getId()) {
            try {
                $template->delete();
                 // display success message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('The email template has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            }
            catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError(__('An error occurred while deleting email template data. Please review log and try again.'));
                Mage::logException($e);
                // save data in session
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('We can\'t find an email template to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    public function previewAction()
    {
        $this->loadLayout('systemPreview');
        $this->renderLayout();
    }

    /**
     * Set template data to retrieve it in template info form
     *
     */
    public function defaultTemplateAction()
    {
        $template = $this->_initTemplate('id');
        $templateCode = $this->getRequest()->getParam('code');
        try {
            $template->loadDefault($templateCode);
            $template->setData('orig_template_code', $templateCode);
            $template->setData('template_variables', Zend_Json::encode($template->getVariablesOptionArray(true)));

            $templateBlock = $this->getLayout()->createBlock('Magento_Adminhtml_Block_System_Email_Template_Edit');
            $template->setData('orig_template_used_default_for', $templateBlock->getUsedDefaultForPaths(false));

            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($template->getData()));
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Load email template from request
     *
     * @param string $idFieldName
     * @return Magento_Adminhtml_Model_Email_Template $model
     */
    protected function _initTemplate($idFieldName = 'template_id')
    {
        $this->_title(__('Email Templates'));

        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('Magento_Adminhtml_Model_Email_Template');
        if ($id) {
            $model->load($id);
        }
        if (!Mage::registry('email_template')) {
            Mage::register('email_template', $model);
        }
        if (!Mage::registry('current_email_template')) {
            Mage::register('current_email_template', $model);
        }
        return $model;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::email_template');
    }
}
