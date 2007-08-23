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
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Tax Rule Information')));

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

        $fieldset->addField('tax_customer_class_id', 'select',
                            array(
                                'name' => 'tax_customer_class_id',
                                'label' => __('Customer Tax Class'),
                                'title' => __('Please, select Customer Tax Class'),
                                'class' => 'required-entry',
                                'required' => true,
                                'values' => $classCustomer,
                                'value' => $ruleObject->getTaxCustomerClassId()
                            )
        );

        $fieldset->addField('tax_product_class_id', 'select',
                            array(
                                'name' => 'tax_product_class_id',
                                'label' => __('Product Tax Class'),
                                'title' => __('Please, select Product Tax Class'),
                                'class' => 'required-entry',
                                'required' => true,
                                'values' => $classProduct,
                                'value' => $ruleObject->getTaxProductClassId()
                            )
        );

        $fieldset->addField('tax_rate_type_id', 'select',
                            array(
                                'name' => 'tax_rate_type_id',
                                'label' => __('Rate'),
                                'title' => __('Please, select Rate'),
                                'class' => 'required-entry',
                                'values' => $rateTypeCollection,
                                'value' => $ruleObject->getTaxRateTypeId()
                            )
        );

        if( $ruleId > 0 ) {
            $fieldset->addField('tax_rule_id', 'hidden',
                                array(
                                    'name' => 'tax_rule_id',
                                    'value' => $ruleId,
                                    'no_span' => true
                                )
            );
        }

        $form->setAction(Mage::getUrl('*/tax_rule/save'));
        $form->setUseContainer(true);
        $form->setId('rule_form');
        $form->setMethod('POST');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
