<?php
/**
 * Web API Role tab with main information.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Main setApiRole(\Magento\Webapi\Model\Acl\Role $role)
 * @method \Magento\Webapi\Model\Acl\Role getApiRole()
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
namespace Magento\Webapi\Block\Adminhtml\Role\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare Form.
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
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
