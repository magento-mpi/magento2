<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invintation create form
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Adminhtml\Invitation\Add;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
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
     * @return \Magento\Invitation\Block\Adminhtml\Invitation\Add\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getActionUrl(),
                'method' => 'post',
            ))
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

        if (!\Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label' => __('Send From'),
                'required' => true,
                'name' => 'store_id',
                'values' => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        }

        $groups = \Mage::getModel('Magento\Customer\Model\Group')->getCollection()
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
     * @return \Magento\Adminhtml\Model\Session
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Session');
    }

}
