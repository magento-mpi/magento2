<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

// @codingStandardsIgnoreStart
/**
 * Scheduled import create/edit form
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
class Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import
    extends Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
{
    /**
     * Prepare form for import operation
     *
     * @return Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Import
     */
    protected function _prepareForm()
    {
        $this->setGeneralSettingsLabel(__('Import Settings'));
        $this->setFileSettingsLabel(__('Import File Information'));
        $this->setEmailSettingsLabel(__('Import Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();

        /** @var $fieldset Magento_Data_Form_Element_Abstract */
        $fieldset = $form->getElement('operation_settings');

        // add behaviour fields
        $importModel = Mage::getModel('Magento_ImportExport_Model_Import');
        $uniqueBehaviors = $importModel->getUniqueEntityBehaviors();
        foreach ($uniqueBehaviors as $behaviorCode => $behaviorClass) {
            /** @var $behaviorSource Magento_ImportExport_Model_Source_Import_BehaviorAbstract */
            $behaviorSource = Mage::getModel($behaviorClass);
            $fieldset->addField($behaviorCode, 'select', array(
                'name'     => 'behavior',
                'title'    => __('Import Behavior'),
                'label'    => __('Import Behavior'),
                'required' => true,
                'disabled' => true,
                'values'   => $behaviorSource->toOptionArray()
            ), 'entity');
        }

        /** @var $operationData Magento_ScheduledImportExport_Model_Scheduled_Operation_Data */
        $operationData = Mage::getSingleton('Magento_ScheduledImportExport_Model_Scheduled_Operation_Data');
        $fieldset->addField('force_import', 'select', array(
            'name'     => 'force_import',
            'title'    => __('On Error'),
            'label'    => __('On Error'),
            'required' => true,
            'values'   => $operationData->getForcedImportOptionArray()
        ), 'freq');

        $form->getElement('email_template')
            ->setValues(Mage::getModel('Magento_Backend_Model_Config_Source_Email_Template')
                ->setPath('magento_scheduledimportexport_import_failed')
                ->toOptionArray()
            );

        $fieldset = $form->getElement('file_settings');
        $fieldset->addField('file_name', 'text', array(
            'name'     => 'file_info[file_name]',
            'title'    => __('File Name'),
            'label'    => __('File Name'),
            'required' => true
        ), 'file_path');

        /** @var $element Magento_Data_Form_Element_Abstract */
        $element = $form->getElement('entity');
        $element->setData('onchange', 'varienImportExportScheduled.handleEntityTypeSelector();');

        /** @var $operation Magento_ScheduledImportExport_Model_Scheduled_Operation */
        $operation = $this->_coreRegistry->registry('current_operation');
        $this->_setFormValues($operation->getData());

        return $this;
    }
}
