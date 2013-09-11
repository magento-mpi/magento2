<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute add/edit form system tab
 */
class Magento_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_System extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('entity_attribute');

        $form = $this->_createForm();
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('System Properties')));

        if ($model->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $yesno = array(
            array(
                'value' => 0,
                'label' => __('No')
            ),
            array(
                'value' => 1,
                'label' => __('Yes')
            ));

        $fieldset->addField('backend_type', 'select', array(
            'name' => 'backend_type',
            'label' => __('Data Type for Saving in Database'),
            'title' => __('Data Type for Saving in Database'),
            'options' => array(
                'text'      => __('Text'),
                'varchar'   => __('Varchar'),
                'static'    => __('Static'),
                'datetime'  => __('Datetime'),
                'decimal'   => __('Decimal'),
                'int'       => __('Integer'),
            ),
        ));

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => __('Globally Editable'),
            'title' => __('Globally Editable'),
            'values'=> $yesno,
        ));

        $form->setValues($model->getData());

        if ($model->getAttributeId()) {
            $form->getElement('backend_type')->setDisabled(1);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
