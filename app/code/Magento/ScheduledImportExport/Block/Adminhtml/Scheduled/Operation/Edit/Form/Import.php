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
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Import setGeneralSettingsLabel() setGeneralSettingsLabel(string $value)
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Import setFileSettingsLabel() setFileSettingsLabel(string $value)
 * @method \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Import setEmailSettingsLabel() setEmailSettingsLabel(string $value)
 */
// @codingStandardsIgnoreEnd
namespace Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form;

class Import
    extends \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form
{
    /**
     * Basic import model
     *
     * @var \Magento\ImportExport\Model\Import
     */
    protected $_importModel;

    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\ImportExport\Model\Import $importModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\ImportExport\Model\Import $importModel,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_importModel = $importModel;
    }

    /**
     * Prepare form for import operation
     *
     * @return \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit\Form\Import
     */
    protected function _prepareForm()
    {
        $this->setGeneralSettingsLabel(__('Import Settings'));
        $this->setFileSettingsLabel(__('Import File Information'));
        $this->setEmailSettingsLabel(__('Import Failed Emails'));

        parent::_prepareForm();
        $form = $this->getForm();

        /** @var $fieldset \Magento\Data\Form\Element\AbstractElement */
        $fieldset = $form->getElement('operation_settings');

        // add behaviour fields
        $uniqueBehaviors = $this->_importModel->getUniqueEntityBehaviors();
        foreach ($uniqueBehaviors as $behaviorCode => $behaviorClass) {
            /** @var $behaviorSource \Magento\ImportExport\Model\Source\Import\BehaviorAbstract */
            $behaviorSource = \Mage::getModel($behaviorClass);
            $fieldset->addField($behaviorCode, 'select', array(
                'name'     => 'behavior',
                'title'    => __('Import Behavior'),
                'label'    => __('Import Behavior'),
                'required' => true,
                'disabled' => true,
                'values'   => $behaviorSource->toOptionArray()
            ), 'entity');
        }

        /** @var $operationData \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data */
        $operationData = \Mage::getSingleton('Magento\ScheduledImportExport\Model\Scheduled\Operation\Data');
        $fieldset->addField('force_import', 'select', array(
            'name'     => 'force_import',
            'title'    => __('On Error'),
            'label'    => __('On Error'),
            'required' => true,
            'values'   => $operationData->getForcedImportOptionArray()
        ), 'freq');

        $form->getElement('email_template')
            ->setValues(\Mage::getModel('Magento\Backend\Model\Config\Source\Email\Template')
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

        /** @var $element \Magento\Data\Form\Element\AbstractElement */
        $element = $form->getElement('entity');
        $element->setData('onchange', 'varienImportExportScheduled.handleEntityTypeSelector();');

        /** @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation */
        $operation = $this->_coreRegistry->registry('current_operation');
        $this->_setFormValues($operation->getData());

        return $this;
    }
}
