<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import edit form block
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Block\Adminhtml\Import\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
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
     * Add fieldsets
     *
     * @return \Magento\ImportExport\Block\Adminhtml\Import\Edit\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'      => 'edit_form',
                'action'  => $this->getUrl('*/*/validate'),
                'method'  => 'post',
                'enctype' => 'multipart/form-data',
            ))
        );

        // base fieldset
        /** @var $importEntity \Magento\ImportExport\Model\Source\Import\Entity */
        $importEntity = \Mage::getModel('Magento\ImportExport\Model\Source\Import\Entity');
        $fieldsets['base'] = $form->addFieldset('base_fieldset', array('legend' => __('Import Settings')));
        $fieldsets['base']->addField('entity', 'select', array(
            'name'     => 'entity',
            'title'    => __('Entity Type'),
            'label'    => __('Entity Type'),
            'required' => true,
            'onchange' => 'varienImport.handleEntityTypeSelector();',
            'values'   => $importEntity->toOptionArray(),
        ));

        // add behaviour fieldsets
        $uniqueBehaviors = $this->_importModel->getUniqueEntityBehaviors();
        foreach ($uniqueBehaviors as $behaviorCode => $behaviorClass) {
            $fieldsets[$behaviorCode] = $form->addFieldset(
                $behaviorCode . '_fieldset',
                array(
                    'legend' => __('Import Behavior'),
                    'class'  => 'no-display',
                )
            );
            /** @var $behaviorSource \Magento\ImportExport\Model\Source\Import\BehaviorAbstract */
            $behaviorSource = \Mage::getModel($behaviorClass);
            $fieldsets[$behaviorCode]->addField($behaviorCode, 'select', array(
                'name'     => 'behavior',
                'title'    => __('Import Behavior'),
                'label'    => __('Import Behavior'),
                'required' => true,
                'disabled' => true,
                'values'   => $behaviorSource->toOptionArray(),
            ));
        }

        // fieldset for file uploading
        $fieldsets['upload'] = $form->addFieldset('upload_file_fieldset',
            array(
                'legend' => __('File to Import'),
                'class'  => 'no-display',
            )
        );
        $fieldsets['upload']->addField(\Magento\ImportExport\Model\Import::FIELD_NAME_SOURCE_FILE, 'file', array(
            'name'     => \Magento\ImportExport\Model\Import::FIELD_NAME_SOURCE_FILE,
            'label'    => __('Select File to Import'),
            'title'    => __('Select File to Import'),
            'required' => true,
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
