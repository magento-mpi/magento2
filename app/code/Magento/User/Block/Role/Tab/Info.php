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
 * implementing now
 *
 */
class Magento_User_Block_Role_Tab_Info
    extends Magento_Backend_Block_Widget_Form_Generic
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return __('Role Info');
    }

    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function _beforeToHtml()
    {
        $this->_initForm();

        return parent::_beforeToHtml();
    }

    protected function _initForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend'=>__('Role Information'))
        );

        $fieldset->addField('role_name', 'text',
            array(
                'name'  => 'rolename',
                'label' => __('Role Name'),
                'id'    => 'role_name',
                'class' => 'required-entry',
                'required' => true,
            )
        );

        $fieldset->addField('role_id', 'hidden',
            array(
                'name'  => 'role_id',
                'id'    => 'role_id',
            )
        );

        $fieldset->addField('in_role_user', 'hidden',
            array(
                'name'  => 'in_role_user',
                'id'    => 'in_role_userz',
            )
        );

        $fieldset->addField('in_role_user_old', 'hidden', array('name' => 'in_role_user_old'));

        $form->setValues($this->getRole()->getData());
        $this->setForm($form);
    }
}
