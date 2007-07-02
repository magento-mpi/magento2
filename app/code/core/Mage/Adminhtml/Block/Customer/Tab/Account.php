<?php
/**
 * Customer account form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Tab_Account extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct() 
    {
        parent::__construct();
        $this->_initForm();
    }
    
    protected function _initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_account');
        $customer = Mage::getModel('customer/customer');
        if ($customerId = (int) $this->getRequest()->getParam('id')) {
            $customer->load($customerId);
        }
            
            
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account information')));
        $fieldset->addField('firstname', 'text', 
            array(
                'name'  => 'firstname',
                'label' => __('Firstname'),
                'id'    => 'customer_firstname',
                'title' => __('Customer Firstname'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('lastname', 'text', 
            array(
                'name'  => 'lastname',
                'label' => __('Lastname'),
                'id'    => 'customer_lastname',
                'title' => __('Customer Lastname'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('email', 'text', 
            array(
                'name'  => 'email',
                'label' => __('Email'),
                'id'    => 'customer_email',
                'title' => __('Customer Email'),
                'class' => 'required-entry validate-email',
            )
        );
        
        $fieldset->addField('group', 'select', 
            array(
                'name'  => 'group_id',
                'label' => __('Group'),
                'id'    => 'customer_group',
                'title' => __('Customer Group'),
                'class' => 'required-entry',
                'values'=> Mage::getResourceModel('customer/group_collection')->load()->toOptionArray()
            )
        );
        
        $fieldset->addField('store_balance', 'text', 
            array(
                'name'  => 'store_balance',
                'label' => __('Balance'),
                'id'    => 'customer_balance',
                'title' => __('Customer Balance'),
                'class' => 'required-entry',
            )
        );
        
        $form->setValues($customer->getData());
       
        if ($customerId) {
            $fieldset->addField('password', 'password', 
                array(
                    'name'  => 'password',
                    'label' => __('New Password'),
                    'id'    => 'customer_pass',
                    'title' => __('New Password'),
                    'class' => 'validate-password',
                )
            );
        }
        else {
           $fieldset->addField('password', 'password', 
                array(
                    'name'  => 'password',
                    'label' => __('Password'),
                    'id'    => 'customer_pass',
                    'title' => __('Password'),
                    'class' => 'required-entry validate-password',
                )
            );
           $fieldset->addField('confirmation', 'password', 
                array(
                    'name'  => 'password_confirmation',
                    'label' => __('Password Confirm'),
                    'id'    => 'confirmation',
                    'title' => __('Password Confirmation'),
                    'class' => 'required-entry validate-cpassword',
                )
            );
        }
        
        /*foreach ($customer->getAttributeCollection() as $attribute) {
        	$fieldset->addField($attribute->getCode(), 'text', 
                array(
                    'name'  => $attribute->getFormFieldName(),
                    'label' => __($attribute->getCode()),
                    'title' => __($attribute->getCode().' title'),
                    'class' => $attribute->getIsRequired() ? 'required-entry' : '',
                    'value' => $customer->getData($attribute->getCode())
                )
            );
        }
        
        if ($element = $form->getElement('password')) {
            $element->setType('password');
            $element->setClass('required-entry validate-password');
            
            if ($customer->getId()) {
                $element->setLabel(__('new password'));
                $element->setTitle(__('new password title'));
            }
            else {
            	$fieldset->addField('confirmation', 'password', 
                    array(
                        'name'  => 'password_confirm',
                        'label' => __('password confirm'),
                        'title' => __('password confirm title'),
                        'class' => 'required-entry validate-cpassword',
                        'value' => $customer->getData($attribute->getCode())
                    ),
                    'password'
                );
            }
        }
        if ($element = $form->getElement('email')) {
            $element->setClass('required-entry validate-email');
        }
        
        */
        $this->setForm($form);
    }
}
