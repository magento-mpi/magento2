<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer admin form
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Block_Customer_Form extends Varien_Data_Form 
{
    public function __construct($customer=null) 
    {
        parent::__construct();
        $this->setId('customer_form');
        $this->setAction(Mage::getUrl('admin/customer/save'));
        
        $customerId = false;
        if ($customer) {
            $customerId = $customer->getId();
        }
        
        $fieldset = $this->addFieldset('base_fieldset', array('legend'=>__('Account Information')));
        
        $fieldset->addField('firstname', 'text', 
            array(
                'name'  => 'firstname',
                'label' => __('First Name'),
                'id'    => 'customer_firstname',
                'title' => __('Customer First Name'),
                'vtype' => 'alphanum',
                'allowBlank' => false
            )
        );

        $fieldset->addField('lastname', 'text', 
            array(
                'name'  => 'lastname',
                'label' => __('Last Name'),
                'id'    => 'customer_lastname',
                'title' => __('Customer Last Name'),
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
                    'inputtype' => 'password',
                    'allowBlank'=> false
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
                    'inputtype' => 'password',
                    'allowBlank'=> false
                )
            );
           $fieldset->addField('password_confirmation', 'password', 
                array(
                    'name'  => 'password_confirmation',
                    'label' => __('Password Confirmation'),
                    'id'    => 'customer_pass',
                    'title' => __('Password Confirmation'),
                    'vtype' => 'alphanum',
                    'inputtype' => 'password',
                    'allowBlank'=> false
                )
            );
        }
    }
}
