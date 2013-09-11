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
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Export setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Export setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Export setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
namespace Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form;

class Export
    extends \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form
{
    /**
     * Prepare form for export operation
     *
     * @return \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Export
     */
    protected function _prepareForm()
    {
        $this->setGeneralSettingsLabel(__('Export Settings'));
        $this->setFileSettingsLabel(__('Export File Information'));
        $this->setEmailSettingsLabel(__('Export Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();
        /** @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation */
        $operation = \Mage::registry('current_operation');

        /** @var $fileFormatModel \Magento\ImportExport\Model\Source\Export\Format */
        $fileFormatModel = \Mage::getModel('Magento\ImportExport\Model\Source\Export\Format');

        $fieldset = $form->getElement('operation_settings');
        $fieldset->addField('file_format', 'select', array(
            'name'      => 'file_info[file_format]',
            'title'     => __('File Format'),
            'label'     => __('File Format'),
            'required'  => true,
            'values'    => $fileFormatModel->toOptionArray()
        ));

        $form->getElement('email_template')
            ->setValues(\Mage::getModel('Magento\Backend\Model\Config\Source\Email\Template')
                ->setPath('magento_scheduledimportexport_export_failed')
                ->toOptionArray()
            );

        /** @var $element \Magento\Data\Form\Element\AbstractElement */
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
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation
     * @return \Magento\ScheduledImportExport\Block\Adminhtml\Export\Filter
     */
    protected function _getFilterBlock($operation)
    {
        $exportOperation = $operation->getInstance();
        /** @var $block \Magento\ScheduledImportExport\Block\Adminhtml\Export\Filter */
        $block = $this->getLayout()
            ->createBlock('Magento\ScheduledImportExport\Block\Adminhtml\Export\Filter')
            ->setOperation($exportOperation);

        $exportOperation->filterAttributeCollection(
            $block->prepareCollection($exportOperation->getEntityAttributeCollection())
        );
        return $block;
    }
}
