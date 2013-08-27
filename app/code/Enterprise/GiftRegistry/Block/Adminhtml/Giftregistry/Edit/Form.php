<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry type edit form block
 */
class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('enterprise_giftregistry_form');
        $this->setTitle(__('Gift Registry'));
    }

    /**
     * Prepare edit form
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
