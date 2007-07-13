<?php
/**
 * Admin tax rule add form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Rule_Form_Add extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $ruleId = $this->getRequest()->getParam('rule');
        $ruleObject = Mage::getSingleton('tax/rule')->load($ruleId);

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Tax rule information')));

        $classCustomer = Mage::getResourceModel('tax/class_collection')
                        ->setClassTypeFilter('CUSTOMER')
                        ->load()
                        ->toOptionArray();

        $classProduct = Mage::getResourceModel('tax/class_collection')
                        ->setClassTypeFilter('PRODUCT')
                        ->load()
                        ->toOptionArray();

        $rateTypeCollection = Mage::getResourceModel('tax/rate_type_collection')
                        ->load()
                        ->toOptionArray();

        $fieldset->addField('customer_tax_class', 'select',
                            array(
                                'name' => 'customer_tax_class',
                                'label' => __('Customer Tax Class'),
                                'title' => __('Please, select Customer Tax Class'),
                                'class' => 'required-entry',
                                'values' => $classCustomer,
                                'value' => $ruleObject->getTaxCustomerClassId()
                            )
        );

        $fieldset->addField('product_tax_class', 'select',
                            array(
                                'name' => 'product_tax_class',
                                'label' => __('Product Tax Class'),
                                'title' => __('Please, select Product Tax Class'),
                                'class' => 'required-entry',
                                'values' => $classProduct,
                                'value' => $ruleObject->getTaxProductClassId()
                            )
        );

        $fieldset->addField('rate_type', 'select',
                            array(
                                'name' => 'rate_type',
                                'label' => __('Rate'),
                                'title' => __('Please, select Rate'),
                                'class' => 'required-entry',
                                'values' => $rateTypeCollection,
                                'value' => $ruleObject->getTaxRateId()
                            )
        );

        if( $ruleId > 0 ) {
            $fieldset->addField('rule_id', 'hidden',
                                array(
                                    'name' => 'rule_id',
                                    'value' => $ruleId,
                                    'no_span' => true
                                )
            );
        }

        $form->setAction(Mage::getUrl('adminhtml/tax_rule/save'));
        $form->setUseContainer(true);
        $form->setId('rule_form');
        $form->setMethod('POST');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}