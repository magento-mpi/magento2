<?php
/**
 * Web API Role tab with main information.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_Main setApiRole(Magento_Webapi_Model_Acl_Role $role)
 * @method Magento_Webapi_Model_Acl_Role getApiRole()
 */
class Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_Main extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Prepare Form.
     *
     * @return Magento_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Role Information'))
        );

        $role = $this->getApiRole();
        if ($role && $role->getId()) {
            $fieldset->addField('role_id', 'hidden', array(
                'name' => 'role_id',
                'value' => $role->getId()
            ));
        }

        $fieldset->addField('role_name', 'text', array(
            'name' => 'role_name',
            'id' => 'role_name',
            'class' => 'required-entry',
            'required' => true,
            'label' => __('Role Name'),
            'title' => __('Role Name'),
        ));

        $fieldset->addField('in_role_user', 'hidden',
            array(
                'name' => 'in_role_user',
                'id' => 'in_role_user',
            )
        );

        $fieldset->addField('in_role_user_old', 'hidden',
            array(
                'name' => 'in_role_user_old'
            )
        );

        if ($role) {
            $form->setValues($role->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
