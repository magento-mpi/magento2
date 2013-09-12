<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms page edit form main tab
 *
 * @category   Magento
 * @package    Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */

class Magento_User_Block_User_Edit_Tab_Main extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return Magento_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /** @var $model Magento_User_Model_User */
        $model = $this->_coreRegistry->registry('permissions_user');

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('user_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend'=>__('Account Information'))
        );

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

        $isNewObject = $model->isObjectNew();
        if ($isNewObject) {
            $passwordLabel = __('Password');
        } else {
            $passwordLabel = __('New Password');
        }
        $confirmationLabel = __('Password Confirmation');
        $this->_addPasswordFields($fieldset, $passwordLabel, $confirmationLabel, $isNewObject);

        $fieldset->addField('interface_locale', 'select', array(
            'name'   => 'interface_locale',
            'label'  => __('Interface Locale'),
            'title'  => __('Interface Locale'),
            'values' => Mage::app()->getLocale()->getTranslatedOptionLocales(),
            'class'  => 'select',
        ));

        if (Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId() != $model->getUserId()) {
            $fieldset->addField('is_active', 'select', array(
                'name'  	=> 'is_active',
                'label' 	=> __('This account is'),
                'id'    	=> 'is_active',
                'title' 	=> __('Account Status'),
                'class' 	=> 'input-select',
                'style'		=> 'width: 80px',
                'options'	=> array(
                    '1' => __('Active'),
                    '0' => __('Inactive')
                ),
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

    /**
     * Add password input fields
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param string $passwordLabel
     * @param string $confirmationLabel
     * @param bool $isRequired
     */
    protected function _addPasswordFields(
        Magento_Data_Form_Element_Fieldset $fieldset, $passwordLabel, $confirmationLabel, $isRequired = false
    ) {
        $requiredFieldClass = ($isRequired ? ' required-entry' : '');
        $fieldset->addField('password', 'password', array(
            'name'  => 'password',
            'label' => $passwordLabel,
            'id'    => 'customer_pass',
            'title' => $passwordLabel,
            'class' => 'input-text validate-admin-password' . $requiredFieldClass,
            'required' => $isRequired,
        ));
        $fieldset->addField('confirmation', 'password', array(
            'name'  => 'password_confirmation',
            'label' => $confirmationLabel,
            'id'    => 'confirmation',
            'title' => $confirmationLabel,
            'class' => 'input-text validate-cpassword' . $requiredFieldClass,
            'required' => $isRequired,
        ));
    }
}
