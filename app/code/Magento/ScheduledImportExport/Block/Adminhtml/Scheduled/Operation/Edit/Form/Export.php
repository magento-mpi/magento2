<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form;

// @codingStandardsIgnoreStart
/**
 * Scheduled export create/edit form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Export setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Export setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Export setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
class Export extends \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form
{
    /**
     * @var \Magento\ImportExport\Model\Source\Export\Format
     */
    protected $_sourceExportFormat;

    /**
     * @var \Magento\Backend\Model\Config\Source\Email\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Magento\ImportExport\Model\Export\ConfigInterface
     */
    protected $_exportConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Option\ArrayPool $optionArrayPool
     * @param \Magento\Backend\Model\Config\Source\Email\Method $emailMethod
     * @param \Magento\Backend\Model\Config\Source\Email\Identity $emailIdentity
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data $operationData
     * @param \Magento\Backend\Model\Config\Source\Yesno $sourceYesno
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Backend\Model\Config\Source\Email\TemplateFactory $templateFactory
     * @param \Magento\ImportExport\Model\Source\Export\Format $sourceExportFormat
     * @param \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Option\ArrayPool $optionArrayPool,
        \Magento\Backend\Model\Config\Source\Email\Method $emailMethod,
        \Magento\Backend\Model\Config\Source\Email\Identity $emailIdentity,
        \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data $operationData,
        \Magento\Backend\Model\Config\Source\Yesno $sourceYesno,
        \Magento\Framework\Stdlib\String $string,
        \Magento\Backend\Model\Config\Source\Email\TemplateFactory $templateFactory,
        \Magento\ImportExport\Model\Source\Export\Format $sourceExportFormat,
        \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig,
        array $data = []
    ) {
        $this->_sourceExportFormat = $sourceExportFormat;
        $this->_templateFactory = $templateFactory;
        $this->_exportConfig = $exportConfig;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $optionArrayPool,
            $emailMethod,
            $emailIdentity,
            $operationData,
            $sourceYesno,
            $string,
            $data
        );
    }

    /**
     * Prepare form for export operation
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->setGeneralSettingsLabel(__('Export Settings'));
        $this->setFileSettingsLabel(__('Export File Information'));
        $this->setEmailSettingsLabel(__('Export Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();
        /** @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation */
        $operation = $this->_coreRegistry->registry('current_operation');

        $fieldset = $form->getElement('operation_settings');
        $fieldset->addField(
            'file_format',
            'select',
            [
                'name' => 'file_info[file_format]',
                'title' => __('File Format'),
                'label' => __('File Format'),
                'required' => true,
                'values' => $this->_sourceExportFormat->toOptionArray()
            ]
        );

        $form->getElement(
            'email_template'
        )->setValues(
            $this->_templateFactory->create()->setPath('magento_scheduledimportexport_export_failed')->toOptionArray()
        );

        /** @var $element \Magento\Framework\Data\Form\Element\AbstractElement */
        $element = $form->getElement('entity');
        $element->setData('onchange', 'varienImportExportScheduled.getFilter();');

        $fieldset = $form->addFieldset(
            'export_filter_grid_container',
            ['legend' => __('Entity Attributes'), 'fieldset_container_id' => 'export_filter_container']
        );

        // prepare filter grid data
        if ($operation->getId()) {
            // $operation object is stored in registry and used in other places.
            // that's why we will not change its data to ensure that existing logic will not be affected.
            // instead we will clone existing operation object.
            $filterOperation = clone $operation;

            $entitiesConfig = $this->_exportConfig->getEntities();
            if (isset($entitiesConfig[$filterOperation->getEntityType()])) {
                $entityConfig = $entitiesConfig[$filterOperation->getEntityType()];
                $filterOperation->setEntityType($entityConfig['entityAttributeFilterType']);
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
        $block = $this->getLayout()->createBlock(
            'Magento\ScheduledImportExport\Block\Adminhtml\Export\Filter'
        )->setOperation(
            $exportOperation
        );

        $exportOperation->filterAttributeCollection(
            $block->prepareCollection($exportOperation->getEntityAttributeCollection())
        );
        return $block;
    }
}
