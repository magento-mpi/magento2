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
class Magento_ImportExport_Block_Adminhtml_Import_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Basic import model
     *
     * @var Magento_ImportExport_Model_Import
     */
    protected $_importModel;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_ImportExport_Model_Import $importModel
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_ImportExport_Model_Import $importModel,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_importModel = $importModel;
    }

    /**
     * Add fieldsets
     *
     * @return Magento_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'      => 'edit_form',
                'action'  => $this->getUrl('*/*/validate'),
                'method'  => 'post',
                'enctype' => 'multipart/form-data',
            ))
        );

        // base fieldset
        /** @var $importEntity Magento_ImportExport_Model_Source_Import_Entity */
        $importEntity = Mage::getModel('Magento_ImportExport_Model_Source_Import_Entity');
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
            /** @var $behaviorSource Magento_ImportExport_Model_Source_Import_BehaviorAbstract */
            $behaviorSource = Mage::getModel($behaviorClass);
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
        $fieldsets['upload']->addField(Magento_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE, 'file', array(
            'name'     => Magento_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE,
            'label'    => __('Select File to Import'),
            'title'    => __('Select File to Import'),
            'required' => true,
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
