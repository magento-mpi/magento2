<?php
/**
 * Web API user edit form.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method Magento_Webapi_Block_Adminhtml_User_Edit setApiUser() setApiUser(Magento_Webapi_Model_Acl_User $user)
 * @method Magento_Webapi_Model_Acl_User getApiUser() getApiUser()
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Webapi_Block_Adminhtml_User_Edit_Tab_Main extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Prepare Form.
     *
     * @return Magento_Webapi_Block_Adminhtml_User_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

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
