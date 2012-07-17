<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

// @codingStandardsIgnoreStart
/**
 * Scheduled import create/edit form
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
class Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import
    extends Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
{
    /**
     * Prepare form for import operation
     *
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import
     */
    protected function _prepareForm()
    {
        /** @var $helper Enterprise_ImportExport_Helper_Data */
        $helper = Mage::helper('Enterprise_ImportExport_Helper_Data');

        $this->setGeneralSettingsLabel($helper->__('Import Settings'));
        $this->setFileSettingsLabel($helper->__('Import File Information'));
        $this->setEmailSettingsLabel($helper->__('Import Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();

        /** @var $behavior Mage_ImportExport_Model_Source_Import_Behavior */
        $behavior = Mage::getModel('Mage_ImportExport_Model_Source_Import_Behavior');

        /** @var $fieldset Varien_Data_Form_Element_Abstract */
        $fieldset = $form->getElement('operation_settings');
        $fieldset->addField('behavior_v1', 'select', array(
            'name'     => 'behavior',
            'title'    => $helper->__('Import Behavior'),
            'label'    => $helper->__('Import Behavior'),
            'required' => true,
            'disabled' => true,
            'values'   => $behavior->toOptionArray()
        ), 'file_format_version');

        /** @var $behavior Mage_ImportExport_Model_Source_Import_Customer_V2_Behavior */
        $behavior = Mage::getModel('Mage_ImportExport_Model_Source_Import_Customer_V2_Behavior');
        $fieldset->addField('behavior_v2_customer', 'select', array(
            'name'     => 'behavior',
            'title'    => $helper->__('Import Behavior'),
            'label'    => $helper->__('Import Behavior'),
            'required' => true,
            'disabled' => true,
            'values'   => $behavior->toOptionArray()
        ), 'behavior_v1');

        /** @var $operationData Enterprise_ImportExport_Model_Scheduled_Operation_Data */
        $operationData = Mage::getSingleton('Enterprise_ImportExport_Model_Scheduled_Operation_Data');
        $fieldset->addField('force_import', 'select', array(
            'name'     => 'force_import',
            'title'    => $helper->__('On Error'),
            'label'    => $helper->__('On Error'),
            'required' => true,
            'values'   => $operationData->getForcedImportOptionArray()
        ), 'freq');

        $form->getElement('email_template')
            ->setValues(Mage::getModel('Mage_Adminhtml_Model_System_Config_Source_Email_Template')
                ->setPath('enterprise_importexport_import_failed')
                ->toOptionArray()
            );

        $fieldset = $form->getElement('file_settings');
        $fieldset->addField('file_name', 'text', array(
            'name'     => 'file_info[file_name]',
            'title'    => $helper->__('File Name'),
            'label'    => $helper->__('File Name'),
            'required' => true
        ), 'file_path');

        /** @var $element Varien_Data_Form_Element_Abstract */
        $element = $form->getElement('entity');
        $element->setData('onchange', 'editForm.handleImportEntityTypeSelector();');

        $element = $form->getElement('file_format_version');
        $element->setData('onchange', 'editForm.handleImportFormatVersionSelector();');

        /** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
        $operation = Mage::registry('current_operation');
        $this->_setFormValues($operation->getData());

        return $this;
    }
}
