<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API permissions User edit form
 *
 * @method Mage_Webapi_Block_Adminhtml_User_Edit setApiUser(Mage_Webapi_Model_Acl_User $user)
 * @method Mage_Webapi_Model_Acl_User getApiUser()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_User_Edit_Form extends Mage_Backend_Block_Widget_Form
{
    /**
     * Prepare Form
     *
     * @return Mage_Webapi_Block_Adminhtml_User_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'method' => 'post',
            'action' => $this->getUrl('*/*/save'),
            'use_container' => true,
            'html_id_prefix' => 'user_'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('Mage_Webapi_Helper_Data')->__('Account Information'))
        );

        $user = $this->getApiUser();
        if ($user->getId()) {
            $fieldset->addField('user_id', 'hidden', array(
                'name' => 'user_id',
                'value' => $user->getId()
            ));
        }

        $fieldset->addField('user_name', 'text', array(
            'name' => 'user_name',
            'id' => 'user_name',
            'required' => true,
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Name'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Name'),
        ));

        /** @var $roleSourceModel Mage_Webapi_Model_Source_Acl_Role */
        $roleSourceModel = Mage::getModel('Mage_Webapi_Model_Source_Acl_Role');
        $fieldset->addField('role_id', 'select', array(
            'name' => 'role_id',
            'id' => 'role_id',
            'required' => true,
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Role'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Role'),
            'values' => $roleSourceModel->toOptionHash()
        ));

        $form->setValues($user->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
