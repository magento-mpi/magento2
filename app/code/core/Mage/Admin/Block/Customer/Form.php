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
        
        $this->addField('firstname', 'text', 
            array(
                'name'  => 'firstname',
                'label' => __('Firstname'),
                'id'    => 'customer_firstname',
                'title' => __('Customer Firstname'),
                'validation'=> '',
            )
        );

        $this->addField('lastname', 'text', 
            array(
                'name'  => 'lastname',
                'label' => __('Lastname'),
                'id'    => 'customer_lastname',
                'title' => __('Customer Lastname'),
                'validation'=> '',
            )
        );

        $this->addField('email', 'text', 
            array(
                'name'  => 'email',
                'label' => __('Email'),
                'id'    => 'customer_email',
                'title' => __('Customer Email'),
                'validation'=> '',
            )
        );
        
        if ($customer) {
            $this->setValues($customer->toArray());
        }
       
        if ($customerId) {
           $this->addField('password', 'password', 
                array(
                    'name'  => 'password',
                    'label' => __('New Password'),
                    'id'    => 'customer_pass',
                    'title' => __('New Password'),
                    'validation'=> '',
                )
            );
        }
        else {
           $this->addField('password', 'password', 
                array(
                    'name'  => 'password',
                    'label' => __('Password'),
                    'id'    => 'customer_pass',
                    'title' => __('Password'),
                    'validation'=> '',
                )
            );
           $this->addField('password_confirmation', 'password', 
                array(
                    'name'  => 'password_confirmation',
                    'label' => __('Password Confirm'),
                    'id'    => 'customer_pass',
                    'title' => __('Password Confirmation'),
                    'validation'=> '',
                )
            );
        }
    }
}
