<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invintation create form
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_Add_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Return invitation form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }

    /**
     * Prepare invitation form
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Invitation_Add_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getActionUrl(),
                'method' => 'post'
            )
        );

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Invitations Information'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('email', 'textarea', array(
            'label' => __('Enter Each Email on New Line'),
            'required' => true,
            'class' => 'validate-emails',
            'name' => 'email'
        ));

        $fieldset->addField('message', 'textarea', array(
            'label' => __('Message'),
            'name' => 'message'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label' => __('Send From'),
                'required' => true,
                'name' => 'store_id',
                'values' => Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }

        $groups = Mage::getModel('Magento_Customer_Model_Group')->getCollection()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $fieldset->addField('group_id', 'select', array(
            'label' => __('Invitee Group'),
            'required' => true,
            'name' => 'group_id',
            'values' => $groups
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setValues($this->_getSession()->getInvitationFormData());

        return parent::_prepareForm();
    }

    /**
     * Return adminhtml session
     *
     * @return Magento_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Adminhtml_Model_Session');
    }

}
