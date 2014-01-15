<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Template admin controller
 *
 * @category   Magento
 * @package    Magento_Email
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Email\Controller\Adminhtml\Email;

class Template extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function indexAction()
    {
        $this->_title->add(__('Email Templates'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Email::template');
        $this->_addBreadcrumb(__('Transactional Emails'), __('Transactional Emails'));
        $this->_view->renderLayout();
    }

    public function gridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
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
        $this->_view->loadLayout();
        $template = $this->_initTemplate('id');
        $this->_setActiveMenu('Magento_Email::template');
        $this->_addBreadcrumb(__('Transactional Emails'), __('Transactional Emails'), $this->getUrl('adminhtml/*'));

        if ($this->getRequest()->getParam('id')) {
            $this->_addBreadcrumb(__('Edit Template'), __('Edit System Template'));
        } else {
            $this->_addBreadcrumb(__('New Template'), __('New System Template'));
        }

        $this->_title->add($template->getId() ? $template->getTemplateCode() : __('New Template'));

        $this->_addContent($this->_view->getLayout()
            ->createBlock('Magento\Email\Block\Adminhtml\Template\Edit', 'template_edit')
            ->setEditMode((bool)$this->getRequest()->getParam('id'))
        );
        $this->_view->renderLayout();
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        $id = $this->getRequest()->getParam('id');

        $template = $this->_initTemplate('id');
        if (!$template->getId() && $id) {
            $this->messageManager->addError(__('This email template no longer exists.'));
            $this->_redirect('adminhtml/*/');
            return;
        }

        try {
            $template->setTemplateSubject($request->getParam('template_subject'))
                ->setTemplateCode($request->getParam('template_code'))
                ->setTemplateText($request->getParam('template_text'))
                ->setTemplateStyles($request->getParam('template_styles'))
                ->setModifiedAt($this->_objectManager->get('Magento\Core\Model\Date')->gmtDate())
                ->setOrigTemplateCode($request->getParam('orig_template_code'))
                ->setOrigTemplateVariables($request->getParam('orig_template_variables'));

            if (!$template->getId()) {
                $template->setTemplateType(\Magento\Email\Model\Template::TYPE_HTML);
            }

            if ($request->getParam('_change_type_flag')) {
                $template->setTemplateType(\Magento\Email\Model\Template::TYPE_TEXT);
                $template->setTemplateStyles('');
            }

            $template->save();
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
            $this->messageManager->addSuccess(__('The email template has been saved.'));
            $this->_redirect('adminhtml/*');
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Backend\Model\Session')
                ->setData('email_template_form_data', $request->getParams());
            $this->messageManager->addError($e->getMessage());
            $this->_forward('new');
        }

    }

    public function deleteAction()
    {
        $template = $this->_initTemplate('id');
        if ($template->getId()) {
            try {
                $template->delete();
                 // display success message
                $this->messageManager->addSuccess(__('The email template has been deleted.'));
                // go to grid
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('An error occurred while deleting email template data. Please review log and try again.')
                );
                $this->_objectManager->get('Magento\Logger')->logException($e);
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')
                    ->setFormData($this->getRequest()->getParams());
                // redirect to edit form
                $this->_redirect('adminhtml/*/edit', array('id' => $template->getId()));
                return;
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find an email template to delete.'));
        // go to grid
        $this->_redirect('adminhtml/*/');
    }

    public function previewAction()
    {
        $this->_view->loadLayout('systemPreview');
        $this->_view->renderLayout();
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
            $template->setData('template_variables', \Zend_Json::encode($template->getVariablesOptionArray(true)));

            $templateBlock = $this->_view->getLayout()->createBlock('Magento\Email\Block\Adminhtml\Template\Edit');
            $template->setData('orig_template_used_default_for', $templateBlock->getUsedDefaultForPaths(false));

            $this->getResponse()->setBody(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($template->getData())
            );
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
    }

    /**
     * Load email template from request
     *
     * @param string $idFieldName
     * @return \Magento\Email\Model\BackendTemplate $model
     */
    protected function _initTemplate($idFieldName = 'template_id')
    {
        $this->_title->add(__('Email Templates'));

        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Magento\Email\Model\BackendTemplate');
        if ($id) {
            $model->load($id);
        }
        if (!$this->_coreRegistry->registry('email_template')) {
            $this->_coreRegistry->register('email_template', $model);
        }
        if (!$this->_coreRegistry->registry('current_email_template')) {
            $this->_coreRegistry->register('current_email_template', $model);
        }
        return $model;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Email::template');
    }
}
