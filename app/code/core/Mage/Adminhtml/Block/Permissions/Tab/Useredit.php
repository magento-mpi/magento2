<?php
class Mage_Adminhtml_Block_Permissions_Tab_Useredit extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    public function _beforeToHtml() {
    	$this->_initForm();

    	return parent::_beforeToHtml();
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();

        $user = $this->getUser();
        $userId = false;
        if (!empty($user)) {
            $userId = $user->getUserId();
        }

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account Information')));

        $fieldset->addField('username', 'text',
            array(
                'name'  => 'username',
                'label' => __('Username'),
                'id'    => 'username',
                'title' => __('Username'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('firstname', 'text',
            array(
                'name'  => 'firstname',
                'label' => __('First Name'),
                'id'    => 'firstname',
                'title' => __('First Name'),
                'class' => 'required-entry',
            )
        );

        $fieldset->addField('lastname', 'text',
            array(
                'name'  => 'lastname',
                'label' => __('Last Name'),
                'id'    => 'lastname',
                'title' => __('Last Name'),
                'class' => 'required-entry',
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
                'label' => __('Email'),
                'id'    => 'customer_email',
                'title' => __('User Email'),
                'class' => 'required-entry validate-email',
            )
        );

        if (!empty($user)) {
            $this->setValues($user->toArray());
        }

        if ($userId) {
            $fieldset->addField('password', 'password',
                array(
                    'name'  => 'new_password',
                    'label' => __('New Password'),
                    'id'    => 'new_pass',
                    'title' => __('New Password'),
                    'class' => 'input-text validate-password',
                )
            );

            $fieldset->addField('confirmation', 'password',
                array(
                    'name'  => 'password_confirmation',
                    'label' => __('Password Confirmation'),
                    'id'    => 'confirmation',
                    'class' => 'input-text validate-cpassword',
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
                    'class' => '',
                )
            );
           $fieldset->addField('confirmation', 'password',
                array(
                    'name'  => 'password_confirmation',
                    'label' => __('Password Confirmation'),
                    'id'    => 'confirmation',
                    'title' => __('Password Confirmation'),
                    'class' => 'validate-confirmation',
                )
            );
        }

        $data = $this->getUser()->getData();

        unset($data['password']);

        $form->setValues($data);
        $this->setForm($form);
    }
}
