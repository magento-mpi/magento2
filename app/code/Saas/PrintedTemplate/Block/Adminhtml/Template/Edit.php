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
 * Adminhtml system template edit block
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_Template_Edit extends Mage_Backend_Block_Widget
{
    const MODULE_NAME = 'Saas_PrintedTemplate';

    /**
     * Internal constructor, that is called from real constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::MODULE_NAME . '::edit.phtml');
    }

    /**
     * Get url for file from module view directory
     *
     * @param string $path
     * @return string
     */
    public function getModuleViewFileUrl($path)
    {
        return $this->getViewFileUrl(
            sprintf('%s::%s', self::MODULE_NAME, $path)
        );
    }

    /**
     * Prepare buttons and form
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Back'),
                        'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                        'class'   => 'back'
                    )
                )
        );
        $this->setChild('reset_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Reset'),
                        'onclick' => 'window.location.href = window.location.href'
                    )
                )
        );
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Delete Template'),
                        'onclick' => 'templateControl.deleteTemplate();',
                        'class'   => 'delete'
                    )
                )
        );
        $this->setChild('preview_html_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Preview HTML'),
                        'onclick' => "templateControl.preview('" . $this->getPreviewHtmlUrl() . "');"
                    )
                )
        );

        $this->setChild('preview_pdf_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Preview PDF'),
                        'onclick' => "templateControl.preview('" . $this->getPreviewPdfUrl() . "');"
                    )
                )
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Save'),
                        'onclick' => 'templateControl.save();',
                        'class'   => 'save'
                    )
                )
        );

        $this->setChild('save_and_continue_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Save And Continue Edit'),
                        'onclick' => 'templateControl.saveAndContinue();',
                        'class'   => 'save'
                    )
                )
        );


        $this->setChild('load_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => $this->__('Load Template'),
                        'onclick' => 'templateControl.load();',
                        'type'    => 'button',
                        'class'   => 'save'
                    )
                )
        );

        $this->setChild('form',
            $this->getLayout()->createBlock('Saas_PrintedTemplate_Block_Adminhtml_Template_Edit_Form')
        );

        return parent::_prepareLayout();
    }

    /**
     * Return edit flag for block
     *
     * @return int
     */
    public function getEditMode()
    {
        return $this->getPrintedTemplate()->getId();
    }

    /**
     * Get list of locales as array
     *
     * @return array
     */
    public function getLocaleOptions()
    {
         return Mage::getSingleton('Saas_PrintedTemplate_Model_Source_AllowedLocales')->toOptionArray();
    }

    /**
     * Get current locale code
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

    /**
     * Get list of available default templates for current type
     *
     * @return array
     */
    public function getTemplateOptions()
    {
        $options = array();

        $idLabel = array();
        foreach (Saas_PrintedTemplate_Model_Template::getDefaultTemplates() as $templateId => $row) {
            if (isset($row['@']) && isset($row['@']['module'])) {
                $module = $row['@']['module'];
            } else {
                $module = 'Saas_PrintedTemplate_Helper_Data';
            }

            if (isset($row['entity_type']) && $row['entity_type'] == $this->getPrintedTemplate()->getEntityType()) {
                $idLabel[$templateId] = Mage::helper($module)->__($row['label']);
            }
        }

        asort($idLabel);
        foreach ($idLabel as $templateId => $label) {
            $options[] = array('value' => $templateId, 'label' => $label);
        }

        return $options;
    }

    /**
     * Returns previously registred template
     *
     * @return type Saas_PrintedTemplate_Model_Template
     */
    public function getPrintedTemplate()
    {
        return Mage::registry('current_printed_template');
    }

    /**
     * Html for button "Back"
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Html for button "Reset"
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Html for button "Preview HTML"
     *
     * @return string
     */
    public function getPreviewHtmlButtonHtml()
    {
        return $this->getChildHtml('preview_html_button');
    }

    /**
     * Html for button "Preview PDF"
     *
     * @return string
     */
    public function getPreviewPdfButtonHtml()
    {
        return $this->getChildHtml('preview_pdf_button');
    }

    /**
     * Html for button "Save"
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Html for button "Save And Continue"
     *
     * @return string
     */
    public function getSaveAndContinueButtonHtml()
    {
        return $this->getChildHtml('save_and_continue_button');
    }

    /**
     * Html for button "Delete"
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Html for Load Template button
     *
     * @return string
     */
    public function getLoadButtonHtml()
    {
        return $this->getChildHtml('load_button');
    }

    /**
     * Return preview HTML action url for form
     *
     * @return string
     */
    public function getPreviewHtmlUrl()
    {
        return $this->getUrl('*/*/previewHtml');
    }

    /**
     * Return preview PDF action url for form
     *
     * @return string
     */
    public function getPreviewPdfUrl()
    {
        return $this->getUrl('*/*/previewPdf');
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'continue_edit' => true));
    }

    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current' => true));
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('form');
    }

    /**
     * Load template url
     *
     * @return string
     */
    public function getLoadUrl()
    {
        return $this->getUrl('*/*/defaultTemplate');
    }

    /**
     * Check Template heights url
     *
     * @return string
     */
    public function getCheckTemplateUrl()
    {
        return $this->getUrl('*/*/checkTemplate');
    }

    /**
     * Returns template type
     *
     * @return string
     */
    public function getTemplateType()
    {
        $str = $this->__('Unknown');
        $types = Mage::getSingleton('Saas_PrintedTemplate_Model_Source_Type')->getAllOptions();
        $currentType = $this->getPrintedTemplate()->getEntityType();
        if (isset($types[$currentType])) {
            $str = $types[$currentType];
        }

        return $this->__($str);
    }

    /**
     * Prepares header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Edit Printed Template (%s)', $this->getTemplateType());
    }
}
