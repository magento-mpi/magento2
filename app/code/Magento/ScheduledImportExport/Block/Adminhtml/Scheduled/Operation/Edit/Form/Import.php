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
     * Basic import model
     *
     * @var Magento_ImportExport_Model_Import
     */
    protected $_importModel;

    /**
     * @var Magento_Backend_Model_Config_Source_Email_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @param Magento_Backend_Model_Config_Source_Email_TemplateFactory $templateFactory
     * @param Magento_Core_Model_Option_ArrayPool $optionArrayPool
     * @param Magento_Backend_Model_Config_Source_Email_Method $emailMethod
     * @param Magento_Backend_Model_Config_Source_Email_Identity $emailIdentity
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation_Data $operationData
     * @param Magento_Backend_Model_Config_Source_Yesno $sourceYesno
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_ImportExport_Model_Import $importModel
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Model_Config_Source_Email_TemplateFactory $templateFactory,
        Magento_Core_Model_Option_ArrayPool $optionArrayPool,
        Magento_Backend_Model_Config_Source_Email_Method $emailMethod,
        Magento_Backend_Model_Config_Source_Email_Identity $emailIdentity,
        Magento_ScheduledImportExport_Model_Scheduled_Operation_Data $operationData,
        Magento_Backend_Model_Config_Source_Yesno $sourceYesno,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_ImportExport_Model_Import $importModel,
        array $data = array()
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_importModel = $importModel;
        parent::__construct(
            $optionArrayPool, $emailMethod, $emailIdentity, $operationData, $sourceYesno, $registry, $formFactory,
            $coreData, $context, $data
        );
    }

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
        $uniqueBehaviors = $this->_importModel->getUniqueEntityBehaviors();
        foreach ($uniqueBehaviors as $behaviorCode => $behaviorClass) {
            $fieldset->addField($behaviorCode, 'select', array(
                'name'     => 'behavior',
                'title'    => __('Import Behavior'),
                'label'    => __('Import Behavior'),
                'required' => true,
                'disabled' => true,
                'values'   => $this->_optionArrayPool->get($behaviorClass)->toOptionArray()
            ), 'entity');
        }

        $fieldset->addField('force_import', 'select', array(
            'name'     => 'force_import',
            'title'    => __('On Error'),
            'label'    => __('On Error'),
            'required' => true,
            'values'   => $this->_operationData->getForcedImportOptionArray()
        ), 'freq');

        $form->getElement('email_template')
            ->setValues($this->_templateFactory->create()
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
