<?php
/**
 * Adminhtml edit admin user account form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_System_Account_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        $user = Mage::getModel('permissions/users')
            ->load($userId);
        $user->unsetData('password');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account Information')));

        $fieldset->addField('username', 'text', array(
                'name'  => 'username',
                'label' => __('User Name'),
                'title' => __('User Name'),
                'required' => true,
            )
        );

        $fieldset->addField('firstname', 'text', array(
                'name'  => 'firstname',
                'label' => __('First Name'),
                'title' => __('First Name'),
                'required' => true,
            )
        );

        $fieldset->addField('lastname', 'text', array(
                'name'  => 'lastname',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
                'required' => true,
            )
        );

        $fieldset->addField('user_id', 'hidden', array(
                'name'  => 'user_id',
            )
        );

        $fieldset->addField('email', 'text', array(
                'name'  => 'email',
                'label' => __('Email'),
                'title' => __('User Email'),
                'required' => true,
            )
        );

        $fieldset->addField('password', 'password', array(
                'name'  => 'password',
                'label' => __('New Password'),
                'title' => __('New Password'),
                'class' => 'input-text validate-password',
            )
        );

        $fieldset->addField('confirmation', 'password', array(
                'name'  => 'password_confirmation',
                'label' => __('Password Confirmation'),
                'class' => 'input-text validate-cpassword',
            )
        );

        $form->setValues($user->getData());
        $form->setAction(Mage::getUrl('*/system_account/save'));
        $form->setMethod('POST');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}