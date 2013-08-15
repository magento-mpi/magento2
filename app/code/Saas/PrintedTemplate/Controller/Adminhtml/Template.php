<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Template controller for create, edit, preview, etc. printed templates
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Controllers
 */
class Saas_PrintedTemplate_Controller_Adminhtml_Template extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_getSession()->addNotice(__('Please make sure that popups are allowed.'));
        $this->_title(__('Printed Templates'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('Saas_PrintedTemplate::template');
        $this->_addBreadcrumb(__('Printed Templates'), __('Printed Templates'));

        $this->_addContent(
            $this->getLayout()->createBlock('Saas_PrintedTemplate_Block_Adminhtml_Template', 'template')
        );
        $this->renderLayout();
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Saas_PrintedTemplate_Block_Adminhtml_Template_Grid')->toHtml()
        );
    }

    /**
     * Edit printed template action
     *
     */
    public function editAction()
    {
        $this->_getSession()->addNotice(__('Please make sure that popups are allowed.'));
        $this->loadLayout(array('default', 'editor'));
        $idFieldName = 'id';
        $template = $this->_initTemplate($idFieldName);
        if (!$template->getId() && $this->getRequest()->getParam($idFieldName)) {
            $this->_getSession()->addError(__('This Printed template no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $this->_setActiveMenu('Saas_PrintedTemplate::template');
        $this->_addBreadcrumb(__('Printed Templates'), __('Printed Templates'), $this->getUrl('*/*'));

        if ($this->getRequest()->getParam('id')) {
            $this->_addBreadcrumb(__('Edit Template'), __('Edit Printed Template'));
        } else {
            $this->_addBreadcrumb(__('New Template'), __('New Printed Template'));
        }

        $this->_title($template->getId() ? $template->getName() : __('New Template'));

        $this->_addContent(
            $this->getLayout()->createBlock(
                'Saas_PrintedTemplate_Block_Adminhtml_Template_Edit',
                'template_edit'
            )
        );
        $this->renderLayout();
    }

    /**
     * Load printed template from request
     *
     * @param string $idFieldName
     * @return Saas_PrintedTemplate_Model_Template $model
     */
    protected function _initTemplate($idFieldName = 'template_id')
    {
        $this->_title(__('Printed Templates'));

        $fieldId = (int)$this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('Saas_PrintedTemplate_Model_Template');
        if ($fieldId) {
            $model->load($fieldId);
        } else {
            $model->setEntityType($this->getRequest()->getParam('entity_type'));
        }
        if (!Mage::registry('printed_template')) {
            Mage::register('printed_template', $model);
        }
        if (!Mage::registry('current_printed_template')) {
            Mage::register('current_printed_template', $model);
        }
        return $model;
    }

    /**
     * Preview HTML of template
     */
    public function previewHtmlAction()
    {
        $templateId = (int)$this->getRequest()->getParam('id');
        if ($templateId) {
            $template = Mage::getModel('Saas_PrintedTemplate_Model_Template')->load($templateId);
        } else {
            $template = Mage::getModel('Saas_PrintedTemplate_Model_Template')
                ->setEntityType($this->getRequest()->getParam('entity_type'));
            $this->_updateTemplate($template);

            try {
                $template->validate();
            } catch (Exception $e) {
                $this->getResponse()->setBody(__($e->getMessage()));
                return;
            }
        }
        Mage::register('printed_template', $template);

        $this->loadLayout('saas_printed_template_preview');
        $this->renderLayout();
    }

    /**
     * Preview PDF of template
     */
    public function previewPdfAction()
    {
        $templateId = (int)$this->getRequest()->getParam('id');
        if ($templateId) {
            $template = Mage::getModel('Saas_PrintedTemplate_Model_Template')->load($templateId);
        } else {
            $template = Mage::getModel('Saas_PrintedTemplate_Model_Template')
                ->setEntityType($this->getRequest()->getParam('entity_type'));
            $this->_updateTemplate($template);
            try {
                $template->validate();
            } catch (Exception $e) {
                $this->getResponse()->setBody(__($e->getMessage()));
                return;
            }
        }

        try {
            $mockModel = Mage::getModel(
                'Saas_PrintedTemplate_Model_Converter_Preview_Mock_' . ucfirst($template->getEntityType())
            )->setOrder(Mage::getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order'));
            $pdf = Mage::helper('Saas_PrintedTemplate_Helper_Locator')->getConverter($mockModel, $template)->getPdf();

            $this->_prepareDownloadResponse(
                'preview' . Mage::getSingleton('Mage_Core_Model_Date')->date('Y-m-d_H-i-s') . '.pdf',
                $pdf,
                'application/pdf'
            );
        } catch (Mage_Core_Exception $e) {
            // @todo Create AJAX validation to display this error in the 'alert' window
            $this->getResponse()->setBody($e->getMessage());
        }
    }

    /**
     * Deletes template
     */
    public function deleteAction()
    {
        $template = $this->_initTemplate('id');
        if (!$template->getId()) {
            $this->_getSession()->addError(__('Unable to find a Printed Template to delete.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $template->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $template->getId()));
            return;
        } catch (Exception $e) {
            $this->_getSession()->addError(
                __(
                    'An error occurred while deleting printed template data. Please review log and try again.'
                )
            );
            Mage::logException($e);
            $this->_redirect('*/*/edit', array('id' => $template->getId()));
            return;
        }

        $this->_getSession()->addSuccess(__('The printed template has been deleted.'));
        $this->_redirect('*/*/');
    }

    /**
     * Saves template
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        $templateId = $request->getParam('id');
        $continueEdit = $request->getParam('continue_edit', false);

        $template = $this->_initTemplate('id');
        if (!$template->getId() && $templateId) {
            $this->_getSession()->addError(__('This Printed template no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_updateTemplate($template);

        try {
            $template->validate();
            $template->save();

            $this->_getSession()
                ->setFormData(false)
                ->addSuccess(__('The Printed template has been saved.'));

            if ($continueEdit) {
                $this->_redirect('*/*/edit', array('id' => $template->getId()));
            } else {
                $this->_redirect('*/*');
            }
        } catch (Exception $e) {
            $this->_getSession()->setData('printed_template_form_data', $request->getParams());
            $this->_getSession()->addError($e->getMessage());
            $this->_forward('edit');
        }
    }

    /**
     * Check if header and footer heights are not greater than allowed
     */
    public function checkTemplateAction()
    {
        $result = array('error' => false);

        $template = $this->_initTemplate('id');
        try {
            $this->_updateTemplate($template);
            $template->validate();
        } catch (Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    /**
     * Updates content, footer, header, page size and orientation from request
     *
     * @param Saas_PrintedTemplate_Model_Template $template
     * @return Saas_PrintedTemplate_Controller_Adminhtml_Template
     */
    protected function _updateTemplate(Saas_PrintedTemplate_Model_Template $template)
    {
        $request = $this->getRequest();
        $lengthType = Saas_PrintedTemplate_Model_RelativeLength::LENGTH_TYPE;

        Mage::getSingleton('Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser')
            ->importContent($request->getParam('content'), $template);

        $template->setName($request->getParam('name'))
            ->setPageOrientation($request->getParam('page_orientation'))
            ->setPageSize($request->getParam('page_size'));

        $template->setHeaderAutoHeight(!is_null($request->getParam('header_auto_height')));
        if ($template->getHeaderAutoHeight() !== true) {
            if ($request->getParam('header_height_measurement') == $lengthType) {
                $headerHeight = Mage::getModel('Saas_PrintedTemplate_Model_RelativeLength',
                    array('percent' => $request->getParam('header_height')));
            } else {
                $headerHeight = new Zend_Measure_Length(
                    (float)$request->getParam('header_height'),
                    $request->getParam('header_height_measurement')
                );
            }
            $template->setHeaderHeight($headerHeight);
        }

        $template->setFooterAutoHeight(null !== $request->getParam('footer_auto_height'));
        if ($template->getFooterAutoHeight() !== true) {
            if ($request->getParam('footer_height_measurement') == $lengthType) {
                $footerHeight = Mage::getModel('Saas_PrintedTemplate_Model_RelativeLength',
                    array('percent' => $request->getParam('footer_height')));
            } else {
                $footerHeight = new Zend_Measure_Length(
                    (float)$request->getParam('footer_height'),
                    $request->getParam('footer_height_measurement')
                );
            }
            $template->setFooterHeight($footerHeight);
        }

        return $this;
    }

    /**
     * Check if allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Saas_PrintedTemplate::add_edit');
    }

    /**
     * Creating new template
     */
    public function newAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Saas_PrintedTemplate::template')
            ->_addBreadcrumb(__('Printed Templates'), __('Printed Templates'), $this->getUrl('*/*'))
            ->_addBreadcrumb(__('New Template'), __('New Printed Template'))
            ->_title(__('New Template'))
            ->_addContent(
                $this->getLayout()->createBlock(
                    'Saas_PrintedTemplate_Block_Adminhtml_Template_New', 'template_edit'
                )
            );

        $this->renderLayout();
    }

    /**
     * Set template data to retrieve it in template info form
     */
    public function defaultTemplateAction()
    {
        $template = $this->_initTemplate('id');
        $templateCode = $this->getRequest()->getParam('code');
        $template->loadDefault($templateCode, $this->getRequest()->getParam('locale'));

        $template->setContent(
            Mage::getSingleton('Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser')->exportContent($template)
        );

        $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($template->getData()));
    }

    /**
     * Get Mage_Core_Model_Variable model
     *
     * @return Mage_Core_Model_Variable
     */
    protected function _getCoreVariableModel()
    {
        return Mage::getModel('Mage_Core_Model_Variable');
    }

    /**
     * Get saas_printedtemplate/source_variables model
     *
     * @return Saas_PrintedTemplate_Model_Source_Variables
     */
    protected function _getPrintedTemplateSourceVariablesModel()
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_Source_Variables');
    }

    /**
     * Get saas_printedtemplate/source_storeVariables
     *
     * @return Saas_PrintedTemplate_Model_Source_StoreVariables
     */
    protected function _getCoreSourceEmailVariablesModel()
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_Source_StoreVariables');
    }

    /**
     * Returns config model
     *
     * @return Saas_PrintedTemplate_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('Saas_PrintedTemplate_Model_Config');
    }

    /**
     * WYSIWYG Plugin variables
     */
    public function wysiwygVariablesAction()
    {
        $customVariables = $this->_getCoreVariableModel()->getVariablesOptionArray(true);
        $storeContactVariabls = $this->_getCoreSourceEmailVariablesModel()->toOptionArray(true);
        $templateVariables = $this->_getPrintedTemplateSourceVariablesModel()
            ->toOptionArray($this->getRequest()->getParam('template_type'));
        array_unshift($templateVariables, $storeContactVariabls);
        $templateVariables[] = $customVariables;
        $this->getResponse()->setBody(Zend_Json::encode($templateVariables));
    }
}
