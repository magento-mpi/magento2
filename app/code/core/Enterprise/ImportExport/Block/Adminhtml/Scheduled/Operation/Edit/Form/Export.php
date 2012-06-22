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
        $this->setGeneralSettingsLabel(Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Export Settings'));
        $this->setFileSettingsLabel(Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Export File Information'));
        $this->setEmailSettingsLabel(Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Export Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();
        $operation = Mage::registry('current_operation');

        $fieldset = $form->getElement('operation_settings');
        $fieldset->addField('file_format', 'select', array(
            'name'      => 'file_info[file_format]',
            'title'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('File Format'),
            'label'     => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('File Format'),
            'required'  => true,
            'values'    => Mage::getModel('Mage_ImportExport_Model_Source_Export_Format_File')->toOptionArray()
        ), 'entity');

        $form->getElement('email_template')
            ->setValues(Mage::getModel('Mage_Adminhtml_Model_System_Config_Source_Email_Template')
                ->setPath('enterprise_importexport_export_failed')
                ->toOptionArray()
            );

        $form->getElement('entity')
            ->setData('onchange', 'editForm.getFilter();');

        $fieldset = $form->addFieldset('export_filter_grid_container', array(
            'legend' => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Entity Attributes'),
            'fieldset_container_id' => 'export_filter_container'
        ));

        if ($operation->getId()) {
            $fieldset->setData('html_content', $this->_getFilterBlock($operation)->toHtml());
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
        $block = $this->getLayout()
            ->createBlock('Enterprise_ImportExport_Block_Adminhtml_Export_Filter')
            ->setOperation($export);

        $export->filterAttributeCollection($block->prepareCollection($export->getEntityAttributeCollection()));
        return $block;
    }
}
