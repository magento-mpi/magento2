<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_User_Block_Tab_Useredit extends Mage_Backend_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $user = Mage::registry('user_data');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('Mage_User_Helper_Data')->__('Account Information')));

        $fieldset->addField('username', 'text',
            array(
                'name'  => 'username',
                'label' => Mage::helper('Mage_User_Helper_Data')->__('User Name'),
                'id'    => 'username',
                'title' => Mage::helper('Mage_User_Helper_Data')->__('User Name'),
                'class' => 'required-entry',
                'required' => true,
            )
        );

        $fieldset->addField('firstname', 'text',
            array(
                'name'  => 'firstname',
                'label' => Mage::helper('Mage_User_Helper_Data')->__('First Name'),
                'id'    => 'firstname',
                'title' => Mage::helper('Mage_User_Helper_Data')->__('First Name'),
                'class' => 'required-entry',
                'required' => true,
            )
        );

        $fieldset->addField('lastname', 'text',
            array(
                'name'  => 'lastname',
                'label' => Mage::helper('Mage_User_Helper_Data')->__('Last Name'),
                'id'    => 'lastname',
                'title' => Mage::helper('Mage_User_Helper_Data')->__('Last Name'),
                'class' => 'required-entry',
                'required' => true,
            )
        );

        $fieldset->addField('user_id', 'hidden',
            array(
                'name'  => 'user_id',
                'id'    => 'user_id',
            )
        );

        $fieldset->addField('email', 'text',
            array(
                'name'  => 'email',
                'label' => Mage::helper('Mage_User_Helper_Data')->__('Email'),
                'id'    => 'customer_email',
                'title' => Mage::helper('Mage_User_Helper_Data')->__('User Email'),
                'class' => 'required-entry validate-email',
                'required' => true,
            )
        );

        if ($user->getUserId()) {
            $fieldset->addField('password', 'password',
                array(
                    'name'  => 'new_password',
                    'label' => Mage::helper('Mage_User_Helper_Data')->__('New Password'),
                    'id'    => 'new_pass',
                    'title' => Mage::helper('Mage_User_Helper_Data')->__('New Password'),
                    'class' => 'input-text validate-password',
                )
            );

            $fieldset->addField('confirmation', 'password',
                array(
                    'name'  => 'password_confirmation',
                    'label' => Mage::helper('Mage_User_Helper_Data')->__('Password Confirmation'),
                    'id'    => 'confirmation',
                    'class' => 'input-text validate-cpassword',
                )
            );
        }
        else {
           $fieldset->addField('password', 'password',
                array(
                    'name'  => 'password',
                    'label' => Mage::helper('Mage_User_Helper_Data')->__('Password'),
                    'id'    => 'customer_pass',
                    'title' => Mage::helper('Mage_User_Helper_Data')->__('Password'),
                    'class' => 'input-text required-entry validate-password',
                    'required' => true,
                )
            );
           $fieldset->addField('confirmation', 'password',
                array(
                    'name'  => 'password_confirmation',
                    'label' => Mage::helper('Mage_User_Helper_Data')->__('Password Confirmation'),
                    'id'    => 'confirmation',
                    'title' => Mage::helper('Mage_User_Helper_Data')->__('Password Confirmation'),
                    'class' => 'input-text required-entry validate-cpassword',
                    'required' => true,
                )
            );
        }

        $fieldset->addField('is_active', 'select',
            array(
                'name'  	=> 'is_active',
                'label' 	=> Mage::helper('Mage_User_Helper_Data')->__('This Account is'),
                'id'    	=> 'is_active',
                'title' 	=> Mage::helper('Mage_User_Helper_Data')->__('Account Status'),
                'class' 	=> 'input-select',
                'required' 	=> false,
                'style'		=> 'width: 80px',
                'value'		=> '1',
                'values'	=> array(
                    array(
                        'label' => Mage::helper('Mage_User_Helper_Data')->__('Active'),
                        'value'	=> '1',
                    ),
                    array(
                        'label' => Mage::helper('Mage_User_Helper_Data')->__('Inactive'),
                        'value' => '0',
                    ),
                ),
            )
        );

        $data = $user->getData();

        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);
    }

}

