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
 * Cms page edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Api_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('api_user');

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
                'name'  => 'new_api_key',
                'label' => __('New API Key'),
                'id'    => 'new_pass',
                'title' => __('New API Key'),
                'class' => 'input-text validate-password',
            ));

            $fieldset->addField('confirmation', 'password', array(
                'name'  => 'api_key_confirmation',
                'label' => __('API Key Confirmation'),
                'id'    => 'confirmation',
                'class' => 'input-text validate-cpassword',
            ));
        }
        else {
           $fieldset->addField('password', 'password', array(
                'name'  => 'api_key',
                'label' => __('API Key'),
                'id'    => 'customer_pass',
                'title' => __('API Key'),
                'class' => 'input-text required-entry validate-password',
                'required' => true,
            ));
           $fieldset->addField('confirmation', 'password', array(
                'name'  => 'api_key_confirmation',
                'label' => __('API Key Confirmation'),
                'id'    => 'confirmation',
                'title' => __('API Key Confirmation'),
                'class' => 'input-text required-entry validate-cpassword',
                'required' => true,
            ));
        }

        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId() != $model->getUserId()) {
            $fieldset->addField('is_active', 'select', array(
                'name'  	=> 'is_active',
                'label' 	=> __('This account is'),
                'id'    	=> 'is_active',
                'title' 	=> __('Account status'),
                'class' 	=> 'input-select',
                'style'		=> 'width: 80px',
                'options'	=> array('1' => __('Active'), '0' => __('Inactive')),
            ));
        }

        $fieldset->addField('user_roles', 'hidden', array(
            'name' => 'user_roles',
            'id'   => '_user_roles',
        ));

        $data = $model->getData();

        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
