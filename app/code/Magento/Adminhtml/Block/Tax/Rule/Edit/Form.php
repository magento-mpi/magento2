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
 * Adminhtml Tax Rule Edit Form
 */
class Magento_Adminhtml_Block_Tax_Rule_Edit_Form extends Magento_Backend_Block_Widget_Form
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Init class
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('taxRuleForm');
        $this->setTitle(__('Tax Rule Information'));
        $this->setUseContainer(true);
    }

    /**
     *
     * return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = $this->_coreRegistry->registry('tax_rule');
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'action'    => $this->getData('action'),
                'method'    => 'post',
            ))
        );

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend' => __('Tax Rule Information')
        ));

        $rates = Mage::getModel('Magento_Tax_Model_Calculation_Rate')
            ->getCollection()
            ->toOptionArray();

         $fieldset->addField('code', 'text',
            array(
                'name'      => 'code',
                'label'     => __('Name'),
                'class'     => 'required-entry',
                'required'  => true,
            )
        );

        // Editable multiselect for customer tax class
        $selectConfig = $this->getTaxClassSelectConfig(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER);
        $selectedCustomerTax = $model->getId()
            ? $model->getCustomerTaxClasses()
            : $model->getCustomerTaxClassWithDefault();
        $fieldset->addField($this->getTaxClassSelectHtmlId(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER),
            'editablemultiselect',
            array(
                'name' => $this->getTaxClassSelectHtmlId(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER),
                'label' => __('Customer Tax Class'),
                'class' => 'required-entry',
                'values' => $model->getAllOptionsForClass(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER),
                'value' => $selectedCustomerTax,
                'required' => true,
                'select_config' => $selectConfig,
            ),
            false,
            true
        );

        // Editable multiselect for product tax class
        $selectConfig = $this->getTaxClassSelectConfig(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT);
        $selectedProductTax = $model->getId()
            ? $model->getProductTaxClasses()
            : $model->getProductTaxClassWithDefault();
        $fieldset->addField($this->getTaxClassSelectHtmlId(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT),
            'editablemultiselect',
            array(
                'name' => $this->getTaxClassSelectHtmlId(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT),
                'label' => __('Product Tax Class'),
                'class' => 'required-entry',
                'values' => $model->getAllOptionsForClass(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT),
                'value' => $selectedProductTax,
                'required' => true,
                'select_config' => $selectConfig
            ),
            false,
            true
        );

        $fieldset->addField('tax_rate',
            'editablemultiselect',
            array(
                'name' => 'tax_rate',
                'label' => __('Tax Rate'),
                'class' => 'required-entry',
                'values' => $rates,
                'value' => $model->getRates(),
                'required' => true,
                'element_js_class' => 'TaxRateEditableMultiselect',
                'select_config' => array('is_entity_editable' => true),
            )
        );

        $fieldset->addField('priority', 'text',
            array(
                'name'      => 'priority',
                'label'     => __('Priority'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPriority(),
                'required'  => true,
                'note'      => __('Tax rates at the same priority are added, others are compounded.'),
            ),
            false,
            true
        );
        $fieldset->addField('position', 'text',
            array(
                'name'      => 'position',
                'label'     => __('Sort Order'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPosition(),
                'required'  => true,
            ),
            false,
            true
        );

        if ($model->getId() > 0 ) {
            $fieldset->addField('tax_calculation_rule_id', 'hidden',
                array(
                    'name'      => 'tax_calculation_rule_id',
                    'value'     => $model->getId(),
                    'no_span'   => true
                )
            );
        }

        $form->addValues($model->getData());
        $form->setAction($this->getUrl('*/tax_rule/save'));
        $form->setUseContainer($this->getUseContainer());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve HTML element ID for corresponding tax class selector
     *
     * @param string $classType
     * @return string
     */
    public function getTaxClassSelectHtmlId($classType)
    {
        return 'tax_' . strtolower($classType) . '_class';
    }


    /**
     * Retrieve configuration options for tax class editable multiselect
     *
     * @param string $classType
     * @return array
     */
    public function getTaxClassSelectConfig($classType)
    {
        $config = array(
            'new_url' => $this->getUrl('adminhtml/tax_class/ajaxSave/'),
            'save_url' => $this->getUrl('adminhtml/tax_class/ajaxSave/'),
            'delete_url' => $this->getUrl('adminhtml/tax_class/ajaxDelete/'),
            'delete_confirm_message' => __('Do you really want to delete this tax class?'),
            'target_select_id' => $this->getTaxClassSelectHtmlId($classType),
            'add_button_caption' => __('Add New Tax Class'),
            'submit_data' => array(
                'class_type' => $classType,
                'form_key' => Mage::getSingleton('Magento_Core_Model_Session')->getFormKey(),
            ),
            'entity_id_name' => 'class_id',
            'entity_value_name' => 'class_name',
            'is_entity_editable' => true
        );
        return $config;
    }

    /**
     * Retrieve Tax Rate delete URL
     *
     * @return string
     */
    public function getTaxRateDeleteUrl()
    {
        return $this->getUrl('adminhtml/tax_rate/ajaxDelete/');
    }

    /**
     * Retrieve Tax Rate save URL
     *
     * @return string
     */
    public function getTaxRateSaveUrl()
    {
        return $this->getUrl('adminhtml/tax_rate/ajaxSave/');
    }
}
