<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scheduled import create/edit form
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
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
        $this->setGeneralSettingsLabel(Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Import Settings'));
        $this->setFileSettingsLabel(Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Import File Information'));
        $this->setEmailSettingsLabel(Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Import Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();

        $fieldset = $form->getElement('operation_settings');
        $fieldset->addField('behavior', 'select', array(
            'name'      => 'behavior',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Import Behavior'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Import Behavior'),
            'required'  => true,
            'values'    => Mage::getModel('Mage_ImportExport_Model_Source_Import_Behavior')->toOptionArray()
        ), 'entity');

        $fieldset->addField('force_import', 'select', array(
            'name'      => 'force_import',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('On Error'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('On Error'),
            'required'  => true,
            'values'    => Mage::getSingleton('Enterprise_ImportExport_Model_Scheduled_Operation_Data')
                ->getForcedImportOptionArray()
        ), 'freq');

        $form->getElement('email_template')
            ->setValues(Mage::getModel('Mage_Adminhtml_Model_System_Config_Source_Email_Template')
                ->setPath('enterprise_importexport_import_failed')
                ->toOptionArray()
            );

        $form->getElement('file_settings')->addField('file_name', 'text', array(
            'name'      => 'file_info[file_name]',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('File Name'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('File Name'),
            'required'  => true
        ), 'file_path');

        $operation = Mage::registry('current_operation');
        $this->_setFormValues($operation->getData());

        return $this;
    }
}
