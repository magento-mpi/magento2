<?php
/**
 * Cms page edit form main tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('permissions_user');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('user_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account Information')));

        if ($model->getUserId()) {
        	$fieldset->addField('user_id', 'hidden', array(
                'name' => 'user_id',
            ));
        } else {
            if (! $model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }

        $fieldset->addField('username', 'text', array(
            'name'  => 'username',
            'label' => __('User Name'),
            'id'    => 'username',
            'title' => __('User Name'),
            'required' => true,
        ));

        $fieldset->addField('firstname', 'text', array(
            'name'  => 'firstname',
            'label' => __('First Name'),
            'id'    => 'firstname',
            'title' => __('First Name'),
            'required' => true,
        ));

        $fieldset->addField('lastname', 'text', array(
            'name'  => 'lastname',
            'label' => __('Last Name'),
            'id'    => 'lastname',
            'title' => __('Last Name'),
            'required' => true,
        ));

        $fieldset->addField('email', 'text', array(
            'name'  => 'email',
            'label' => __('Email'),
            'id'    => 'customer_email',
            'title' => __('User Email'),
            'class' => 'required-entry validate-email',
            'required' => true,
        ));

        if ($model->getUserId()) {
            $fieldset->addField('password', 'password', array(
                'name'  => 'new_password',
                'label' => __('New Password'),
                'id'    => 'new_pass',
                'title' => __('New Password'),
                'class' => 'input-text validate-password',
            ));

            $fieldset->addField('confirmation', 'password', array(
                'name'  => 'password_confirmation',
                'label' => __('Password Confirmation'),
                'id'    => 'confirmation',
                'class' => 'input-text validate-cpassword',
            ));
        }
        else {
           $fieldset->addField('password', 'password', array(
                'name'  => 'password',
                'label' => __('Password'),
                'id'    => 'customer_pass',
                'title' => __('Password'),
                'class' => 'input-text required-entry validate-password',
                'required' => true,
            ));
           $fieldset->addField('confirmation', 'password', array(
                'name'  => 'password_confirmation',
                'label' => __('Password Confirmation'),
                'id'    => 'confirmation',
                'title' => __('Password Confirmation'),
                'class' => 'input-text required-entry validate-cpassword',
                'required' => true,
            ));
        }

        $fieldset->addField('is_active', 'select', array(
            'name'  	=> 'is_active',
            'label' 	=> __('This account is'),
            'id'    	=> 'is_active',
            'title' 	=> __('Account status'),
            'class' 	=> 'input-select',
            'style'		=> 'width: 80px',
            'options'	=> array('1' => __('Active'), '0' => __('Inactive')),
        ));

        $data = $model->getData();

        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
