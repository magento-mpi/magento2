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
 * Manage Newsletter Template Controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Newsletter_Template extends Magento_Adminhtml_Controller_Action
{
    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed ()
    {
        return $this->_authorization
            ->isAllowed('Magento_Newsletter::template');
    }

    /**
     * Set title of page
     *
     * @return Magento_Adminhtml_Controller_Newsletter_Template
     */
    protected function _setTitle()
    {
        return $this->_title(__('Newsletter Templates'));
    }

    /**
     * View Templates list
     *
     */
    public function indexAction ()
    {
        $this->_setTitle();

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();
        $this->_setActiveMenu('Magento_Newsletter::newsletter_template');
        $this->_addBreadcrumb(__('Newsletter Templates'), __('Newsletter Templates'));
        $this->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Newsletter_Template', 'template'));
        $this->renderLayout();
    }

    /**
     * JSON Grid Action
     *
     */
    public function gridAction ()
    {
        $this->loadLayout();
        $grid = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Newsletter_Template_Grid')
            ->toHtml();
        $this->getResponse()->setBody($grid);
    }

    /**
     * Create new Newsletter Template
     *
     */
    public function newAction ()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Newsletter Template
     *
     */
    public function editAction ()
    {
        $this->_setTitle();

        $model = Mage::getModel('Magento_Newsletter_Model_Template');
        if ($id = $this->getRequest()->getParam('id')) {
            $model->load($id);
        }

        Mage::register('_current_template', $model);

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Newsletter::newsletter_template');

        if ($model->getId()) {
            $breadcrumbTitle = __('Edit Template');
            $breadcrumbLabel = $breadcrumbTitle;
        }
        else {
            $breadcrumbTitle = __('New Template');
            $breadcrumbLabel = __('Create Newsletter Template');
        }

        $this->_title($model->getId() ? $model->getTemplateCode() : __('New Template'));

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        // restore data
        if ($values = $this->_getSession()->getData('newsletter_template_form_data', true)) {
            $model->addData($values);
        }

        if ($editBlock = $this->getLayout()->getBlock('template_edit')) {
            $editBlock->setEditMode($model->getId() > 0);
        }

        $this->renderLayout();
    }

    /**
     * Drop Newsletter Template
     *
     */
    public function dropAction ()
    {
        $this->loadLayout('newsletter_template_preview');
        $this->renderLayout();
    }

    /**
     * Save Newsletter Template
     *
     */
    public function saveAction ()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/newsletter_template'));
        }
        $template = Mage::getModel('Magento_Newsletter_Model_Template');

        if ($id = (int)$request->getParam('id')) {
            $template->load($id);
        }

        try {
            $template->addData($request->getParams())
                ->setTemplateSubject($request->getParam('subject'))
                ->setTemplateCode($request->getParam('code'))
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
                ->setTemplateText($request->getParam('text'))
                ->setTemplateStyles($request->getParam('styles'))
                ->setModifiedAt(Mage::getSingleton('Magento_Core_Model_Date')->gmtDate());

            if (!$template->getId()) {
                $template->setTemplateType(Magento_Newsletter_Model_Template::TYPE_HTML);
            }
            if ($this->getRequest()->getParam('_change_type_flag')) {
                $template->setTemplateType(Magento_Newsletter_Model_Template::TYPE_TEXT);
                $template->setTemplateStyles('');
            }
            if ($this->getRequest()->getParam('_save_as_flag')) {
                $template->setId(null);
            }

            $template->save();

            $this->_getSession()->addSuccess(__('The newsletter template has been saved.'));
            $this->_getSession()->setFormData(false);

            $this->_redirect('*/*');
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('newsletter_template_form_data',
                $this->getRequest()->getParams());
        } catch (Exception $e) {
            $this->_getSession()->addException($e,
                __('An error occurred while saving this template.')
            );
            $this->_getSession()->setData('newsletter_template_form_data', $this->getRequest()->getParams());
        }

        $this->_forward('new');
    }

    /**
     * Delete newsletter Template
     *
     */
    public function deleteAction ()
    {
        $template = Mage::getModel('Magento_Newsletter_Model_Template')
            ->load($this->getRequest()->getParam('id'));
        if ($template->getId()) {
            try {
                $template->delete();
                $this->_getSession()->addSuccess(__('The newsletter template has been deleted.'));
                $this->_getSession()->setFormData(false);
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    __('An error occurred while deleting this template.')
                );
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * Preview Newsletter template
     *
     */
    public function previewAction ()
    {
        $this->_setTitle();
        $this->loadLayout();

        $data = $this->getRequest()->getParams();
        if (empty($data) || !isset($data['id'])) {
            $this->_forward('noRoute');
            return $this;
        }

        // set default value for selected store
        $data['preview_store_id'] = Mage::app()->getDefaultStoreView()->getId();

        $this->getLayout()->getBlock('preview_form')->setFormData($data);
        $this->renderLayout();
    }
}
