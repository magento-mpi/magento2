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
 * Web API user edit form
 *
 * @method Mage_Webapi_Block_Adminhtml_User_Edit setApiUser(Mage_Webapi_Model_Acl_User $user)
 * @method Mage_Webapi_Model_Acl_User getApiUser()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Main extends Mage_Backend_Block_Widget_Form
{
    /**
     * Prepare Form
     *
     * @return Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('user_');

        $fieldset = $form->addFieldset('base_fieldset', array(
                'legend'=>Mage::helper('Mage_Webapi_Helper_Data')->__('Account Information'))
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
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('Company Name'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('Company Name'),
        ));

        $fieldset->addField('contact_email', 'text', array(
            'name' => 'contact_email',
            'id' => 'contact_email',
            'class' => 'validate-email',
            'required' => true,
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('Contact Email'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('Contact Email'),
        ));

        $fieldset->addField('api_key', 'text', array(
            'name' => 'api_key',
            'id' => 'api_key',
            'required' => true,
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('API Key'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('API Key'),
        ));

        $fieldset->addField('api_secret', 'text', array(
            'name' => 'api_secret',
            'id' => 'api_secret',
            'required' => true,
            'label' => Mage::helper('Mage_Webapi_Helper_Data')->__('API Secret'),
            'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('API Secret'),
        ));

        $form->setValues($user->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
