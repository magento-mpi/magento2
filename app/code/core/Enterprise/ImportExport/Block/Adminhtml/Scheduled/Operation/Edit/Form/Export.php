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
 * Scheduled export create/edit form
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export
    extends Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form
{
    /**
     * Prepare form for export operation
     *
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_Export
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('Enterprise_ImportExport_Helper_Data');

        $this->setGeneralSettingsLabel($helper->__('Export Settings'));
        $this->setFileSettingsLabel($helper->__('Export File Information'));
        $this->setEmailSettingsLabel($helper->__('Export Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();
        /** @var $operation Enterprise_ImportExport_Model_Scheduled_Operation */
        $operation = Mage::registry('current_operation');

        /** @var $fileFormatModel Mage_ImportExport_Model_Source_Export_Format_File */
        $fileFormatModel = Mage::getModel('Mage_ImportExport_Model_Source_Export_Format_File');

        $fieldset = $form->getElement('operation_settings');
        $fieldset->addField('file_format', 'select', array(
            'name'      => 'file_info[file_format]',
            'title'     => $helper->__('File Format'),
            'label'     => $helper->__('File Format'),
            'required'  => true,
            'values'    => $fileFormatModel->toOptionArray()
        ), 'entity_subtype');

        $form->getElement('email_template')
            ->setValues(Mage::getModel('Mage_Adminhtml_Model_System_Config_Source_Email_Template')
                ->setPath('enterprise_importexport_export_failed')
                ->toOptionArray()
            );

        /** @var $element Varien_Data_Form_Element_Abstract */
        $element = $form->getElement('entity');
        $element->setData('onchange', 'editForm.handleExportEntityTypeSelector();');

        $element = $form->getElement('file_format_version');
        $element->setData('onchange', 'editForm.handleExportFormatVersionSelector();');

        $element = $form->getElement('entity_subtype');
        $element->setData('onchange', 'editForm.handleCustomerEntityTypeSelector();');

        $fieldset = $form->addFieldset('export_filter_grid_container', array(
            'legend' => $helper->__('Entity Attributes'),
            'fieldset_container_id' => 'export_filter_container'
        ));

        // prepare filter grid data
        if ($operation->getId()) {
            $filterOperation = clone $operation;
            if ($filterOperation->getEntitySubtype()) {
                $filterOperation->setEntitySubtype('customer');
            }
            $fieldset->setData('html_content', $this->_getFilterBlock($filterOperation)->toHtml());
        }

        $this->_setFormValues($operation->getData());

        return $this;
    }

    /**
     * Return block instance with specific attribute fields
     *
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return Enterprise_ImportExport_Block_Adminhtml_Export_Filter
     */
    protected function _getFilterBlock($operation)
    {
        $export = $operation->getInstance();
        /** @var $block Enterprise_ImportExport_Block_Adminhtml_Export_Filter */
        $block = $this->getLayout()
            ->createBlock('Enterprise_ImportExport_Block_Adminhtml_Export_Filter')
            ->setOperation($export);

        $export->filterAttributeCollection($block->prepareCollection($export->getEntityAttributeCollection()));
        return $block;
    }
}
