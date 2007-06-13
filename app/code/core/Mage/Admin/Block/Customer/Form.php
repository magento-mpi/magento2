<?php
/**
 * Customer admin form
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Block_Customer_Form extends Varien_Data_Form 
{
    public function __construct($customer=null) 
    {
        parent::__construct();
        $this->setId('customer_form');
        $this->setAction(Mage::getUrl('admin', array('controller'=>'customer', 'action'=>'save')));
        
        $customerId = false;
        if ($customer) {
            $customerId = $customer->getId();
        }
        
        $fieldset = $this->addFieldset('base_fieldset', array('legend'=>__('Account information')));
        
        $fieldset->addField('firstname', 'text', 
            array(
                'name'  => 'firstname',
                'label' => __('Firstname'),
                'id'    => 'customer_firstname',
                'title' => __('Customer Firstname'),
                'vtype' => 'alphanum',
                'allowBlank' => false
            )
        );

        $fieldset->addField('lastname', 'text', 
            array(
                'name'  => 'lastname',
                'label' => __('Lastname'),
                'id'    => 'customer_lastname',
                'title' => __('Customer Lastname'),
                'vtype' => 'alphanum',
                'allowBlank' => false
            )
        );

        $fieldset->addField('email', 'text', 
            array(
                'name'  => 'email',
                'label' => __('Email'),
                'id'    => 'customer_email',
                'title' => __('Customer Email'),
                'vtype' => 'email',
                'allowBlank' => false
            )
        );
        
        if ($customer) {
            $this->setValues($customer->toArray());
        }
       
        if ($customerId) {
            $fieldset->addField('password', 'password', 
                array(
                    'name'  => 'password',
                    'label' => __('New Password'),
                    'id'    => 'customer_pass',
                    'title' => __('New Password'),
                    'vtype' => 'alphanum',
                    'allowBlank' => false
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
                    'vtype' => 'alphanum',
                    'allowBlank' => false
                )
            );
           $fieldset->addField('password_confirmation', 'password', 
                array(
                    'name'  => 'password_confirmation',
                    'label' => __('Password Confirm'),
                    'id'    => 'customer_pass',
                    'title' => __('Password Confirmation'),
                    'vtype' => 'alphanum',
                    'allowBlank' => false
                )
            );
        }
    }
}
