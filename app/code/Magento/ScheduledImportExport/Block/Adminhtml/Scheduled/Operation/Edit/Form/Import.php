<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form;

// @codingStandardsIgnoreStart
/**
 * Scheduled import create/edit form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Import setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Import setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Import setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
class Import extends \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form
{
    /**
     * Basic import model
     *
     * @var \Magento\ImportExport\Model\Import
     */
    protected $_importModel;

    /**
     * @var \Magento\Backend\Model\Config\Source\Email\TemplateFactory
     */
    protected $_templateFactory;

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
     * @param \Magento\ImportExport\Model\Import $importModel
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
        \Magento\ImportExport\Model\Import $importModel,
        array $data = []
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_importModel = $importModel;
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
     * Prepare form for import operation
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->setGeneralSettingsLabel(__('Import Settings'));
        $this->setFileSettingsLabel(__('Import File Information'));
        $this->setEmailSettingsLabel(__('Import Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();

        /** @var $fieldset \Magento\Framework\Data\Form\Element\AbstractElement */
        $fieldset = $form->getElement('operation_settings');

        // add behaviour fields
        $uniqueBehaviors = $this->_importModel->getUniqueEntityBehaviors();
        foreach ($uniqueBehaviors as $behaviorCode => $behaviorClass) {
            $fieldset->addField(
                $behaviorCode,
                'select',
                [
                    'name' => 'behavior',
                    'title' => __('Import Behavior'),
                    'label' => __('Import Behavior'),
                    'required' => true,
                    'disabled' => true,
                    'values' => $this->_optionArrayPool->get($behaviorClass)->toOptionArray()
                ],
                'entity'
            );
        }

        $fieldset->addField(
            'force_import',
            'select',
            [
                'name' => 'force_import',
                'title' => __('On Error'),
                'label' => __('On Error'),
                'required' => true,
                'values' => $this->_operationData->getForcedImportOptionArray()
            ],
            'freq'
        );

        $form->getElement(
            'email_template'
        )->setValues(
            $this->_templateFactory->create()->setPath('magento_scheduledimportexport_import_failed')->toOptionArray()
        );

        $fieldset = $form->getElement('file_settings');
        $fieldset->addField(
            'file_name',
            'text',
            [
                'name' => 'file_info[file_name]',
                'title' => __('File Name'),
                'label' => __('File Name'),
                'required' => true
            ],
            'file_path'
        );

        /** @var $element \Magento\Framework\Data\Form\Element\AbstractElement */
        $element = $form->getElement('entity');
        $element->setData('onchange', 'varienImportExportScheduled.handleEntityTypeSelector();');

        /** @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation */
        $operation = $this->_coreRegistry->registry('current_operation');
        $this->_setFormValues($operation->getData());

        return $this;
    }
}
