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
        
        $customer = Mage::getModel('customer/entity');
        if ($id = $this->_request->getParam('id')) {
            $customer->load($id);
        }
            
            
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account information')));
        
        foreach ($customer->getAttributeCollection() as $attribute) {
        	$fieldset->addField($attribute->getCode(), 'text', 
                array(
                    'name'  => $attribute->getFormFieldName(),
                    'label' => __($attribute->getCode()),
                    'id'    => $attribute->getCode(),
                    'title' => __($attribute->getCode().' title'),
                    'class' => $attribute->getIsRequired() ? 'required-entry' : '',
                    'value' => $customer->getData($attribute->getCode())
                )
            );
        }
        
        /*$fieldset->addField('firstname', 'text', 
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
        );*/
        
        /*if ($customer) {
            $this->setValues($customer->toArray());
        }*/
       
        /*if ($customerId) {
            $fieldset->addField('password', 'password', 
                array(
                    'name'  => 'password',
                    'label' => __('New Password'),
                    'id'    => 'customer_pass',
                    'title' => __('New Password'),
                    'class' => 'required-entry',
                )
            );
        }
        else {*/
           /*$fieldset->addField('password', 'password', 
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
            );*/
        //}
        
        $this->setForm($form);
    }
}
