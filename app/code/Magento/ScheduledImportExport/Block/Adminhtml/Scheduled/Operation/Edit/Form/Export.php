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
 * Scheduled export create/edit form
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
class Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export
    extends Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
{
    /**
     * @var Magento_ImportExport_Model_Source_Export_Format
     */
    protected $_sourceExportFormat;

    /**
     * @var Magento_Backend_Model_Config_Source_Email_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @param Magento_Backend_Model_Config_Source_Email_TemplateFactory $templateFactory
     * @param Magento_ImportExport_Model_Source_Export_Format $sourceExportFormat
     * @param Magento_Core_Model_Option_ArrayPool $optionArrayPool
     * @param Magento_Backend_Model_Config_Source_Email_Method $emailMethod
     * @param Magento_Backend_Model_Config_Source_Email_Identity $emailIdentity
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation_Data $operationData
     * @param Magento_Backend_Model_Config_Source_Yesno $sourceYesno
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Backend_Model_Config_Source_Email_TemplateFactory $templateFactory,
        Magento_ImportExport_Model_Source_Export_Format $sourceExportFormat,
        Magento_Core_Model_Option_ArrayPool $optionArrayPool,
        Magento_Backend_Model_Config_Source_Email_Method $emailMethod,
        Magento_Backend_Model_Config_Source_Email_Identity $emailIdentity,
        Magento_ScheduledImportExport_Model_Scheduled_Operation_Data $operationData,
        Magento_Backend_Model_Config_Source_Yesno $sourceYesno,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_sourceExportFormat = $sourceExportFormat;
        $this->_templateFactory = $templateFactory;
        parent::__construct(
            $optionArrayPool, $emailMethod, $emailIdentity, $operationData, $sourceYesno, $registry, $formFactory,
            $coreData, $context, $data
        );
    }

    /**
     * Prepare form for export operation
     *
     * @return Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export
     */
    protected function _prepareForm()
    {
        $this->setGeneralSettingsLabel(__('Export Settings'));
        $this->setFileSettingsLabel(__('Export File Information'));
        $this->setEmailSettingsLabel(__('Export Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();
        /** @var $operation Magento_ScheduledImportExport_Model_Scheduled_Operation */
        $operation = $this->_coreRegistry->registry('current_operation');

        $fieldset = $form->getElement('operation_settings');
        $fieldset->addField('file_format', 'select', array(
            'name'      => 'file_info[file_format]',
            'title'     => __('File Format'),
            'label'     => __('File Format'),
            'required'  => true,
            'values'    => $this->_sourceExportFormat->toOptionArray()
        ));

        $form->getElement('email_template')
            ->setValues($this->_templateFactory->create()
                ->setPath('magento_scheduledimportexport_export_failed')
                ->toOptionArray()
            );

        /** @var $element Magento_Data_Form_Element_Abstract */
        $element = $form->getElement('entity');
        $element->setData('onchange', 'varienImportExportScheduled.getFilter();');

        $fieldset = $form->addFieldset('export_filter_grid_container', array(
            'legend' => __('Entity Attributes'),
            'fieldset_container_id' => 'export_filter_container'
        ));

        // prepare filter grid data
        if ($operation->getId()) {
            // $operation object is stored in registry and used in other places.
            // that's why we will not change its data to ensure that existing logic will not be affected.
            // instead we will clone existing operation object.
            $filterOperation = clone $operation;
            if ($filterOperation->getEntityType() == 'customer_address'
                || $filterOperation->getEntityType() == 'customer_finance'
            ) {
                $filterOperation->setEntityType('customer');
            }
            $fieldset->setData('html_content', $this->_getFilterBlock($filterOperation)->toHtml());
        }

        $this->_setFormValues($operation->getData());

        return $this;
    }

    /**
     * Return block instance with specific attribute fields
     *
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return Magento_ScheduledImportExport_Block_Adminhtml_Export_Filter
     */
    protected function _getFilterBlock($operation)
    {
        $exportOperation = $operation->getInstance();
        /** @var $block Magento_ScheduledImportExport_Block_Adminhtml_Export_Filter */
        $block = $this->getLayout()
            ->createBlock('Magento_ScheduledImportExport_Block_Adminhtml_Export_Filter')
            ->setOperation($exportOperation);

        $exportOperation->filterAttributeCollection(
            $block->prepareCollection($exportOperation->getEntityAttributeCollection())
        );
        return $block;
    }
}
