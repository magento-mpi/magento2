<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Customer_Edit_Sharing
    extends Magento_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = $this->_createForm(array(
            'id' => 'edit_form',
            'action' => $this->getActionUrl(),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Sharing Information'),
            'class'  => 'fieldset-wide'
        ));

        $fieldset->addField('emails', 'text', array(
            'label'    => __('Emails'),
            'required' => true,
            'class'    => 'validate-emails',
            'name'     => 'emails',
            'note'     => 'Enter list of emails, comma-separated.'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'select', array(
                'label'    => __('Send From'),
                'required' => true,
                'name'     => 'store_id',
                'values'   => Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreValuesForForm()
            ));
        }

        $fieldset->addField('message', 'textarea', array(
            'label' => __('Message'),
            'name'  => 'message',
            'style' => 'height: 50px;',
            'after_element_html' => $this->getShareButton()
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setDataObject();

        return parent::_prepareForm();
    }

    /**
     * Return sharing form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/share', array('_current' => true));
    }

    /**
     * Create button
     *
     * @return string
     */
    public function getShareButton()
    {
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->addData(array(
                'id'      => '',
                'label'   => __('Share Gift Registry'),
                'type'    => 'submit'
            ))->toHtml();
    }
}
