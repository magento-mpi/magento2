<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Tax Rule Edit Form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Rule_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('taxRuleForm');
        $this->setTitle(Mage::helper('Mage_Tax_Helper_Data')->__('Tax Rule Information'));
    }

    /**
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = Mage::registry('tax_rule');
        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('Mage_Tax_Helper_Data')->__('Tax Rule Information')
        ));

        $productClasses = Mage::getModel('Mage_Tax_Model_Class')
            ->getCollection()
            ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
            ->toOptionArray();

        $customerClasses = Mage::getModel('Mage_Tax_Model_Class')
            ->getCollection()
            ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
            ->toOptionArray();

        $rates = Mage::getModel('Mage_Tax_Model_Calculation_Rate')
            ->getCollection()
            ->toOptionArray();

        $fieldset->addField('code', 'text',
            array(
                'name'      => 'code',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Name'),
                'class'     => 'required-entry',
                'required'  => true,
            )
        );

        $fieldset->addField('tax_customer_class', 'multiselect',
            array(
                'name'      => 'tax_customer_class',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Customer Tax Class'),
                'class'     => 'required-entry',
                'values'    => $customerClasses,
                'value'     => $model->getCustomerTaxClasses(),
                'required'  => true,
            )
        );

        $fieldset->addField('tax_product_class', 'multiselect',
            array(
                'name'      => 'tax_product_class',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Product Tax Class'),
                'class'     => 'required-entry',
                'values'    => $productClasses,
                'value'     => $model->getProductTaxClasses(),
                'required'  => true,
            )
        );

        $fieldset->addField('tax_rate', 'multiselect',
            array(
                'name'      => 'tax_rate',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Tax Rate'),
                'class'     => 'required-entry',
                'values'    => $rates,
                'value'     => $model->getRates(),
                'required'  => true,
            )
        );
        $fieldset->addField('priority', 'text',
            array(
                'name'      => 'priority',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Priority'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPriority(),
                'required'  => true,
                'note'      => Mage::helper('Mage_Tax_Helper_Data')->__('Tax rates at the same priority are added, others are compounded.'),
            )
        );
        $fieldset->addField('position', 'text',
            array(
                'name'      => 'position',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Sort Order'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPosition(),
                'required'  => true,
            )
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
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
