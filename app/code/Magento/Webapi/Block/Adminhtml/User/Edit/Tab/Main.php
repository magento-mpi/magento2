<?php
/**
 * Web API user edit form.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Webapi\Block\Adminhtml\User\Edit setApiUser() setApiUser(\Magento\Webapi\Model\Acl\User $user)
 * @method \Magento\Webapi\Model\Acl\User getApiUser() getApiUser()
 */
namespace Magento\Webapi\Block\Adminhtml\User\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Prepare Form.
     *
     * @return \Magento\Webapi\Block\Adminhtml\User\Edit\Tab\Main
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Account Information'))
        );

        $user = $this->getApiUser();
        if ($user->getId()) {
            $fieldset->addField('user_id', 'hidden', array(
                'name' => 'user_id',
                'value' => $user->getId()
            ));
        }

        $fieldset->addField('company_name', 'text', array(
            'name' => 'company_name',
            'id' => 'company_name',
            'required' => false,
            'label' => __('Company Name'),
            'title' => __('Company Name'),
        ));

        $fieldset->addField('contact_email', 'text', array(
            'name' => 'contact_email',
            'id' => 'contact_email',
            'class' => 'validate-email',
            'required' => true,
            'label' => __('Contact Email'),
            'title' => __('Contact Email'),
        ));

        $fieldset->addField('api_key', 'text', array(
            'name' => 'api_key',
            'id' => 'api_key',
            'required' => true,
            'label' => __('API Key'),
            'title' => __('API Key'),
        ));

        $fieldset->addField('secret', 'text', array(
            'name' => 'secret',
            'id' => 'secret',
            'required' => true,
            'label' => __('API Secret'),
            'title' => __('API Secret'),
        ));

        if ($user) {
            $form->setValues($user->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
