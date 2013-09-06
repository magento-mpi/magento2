<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry type edit form block
 */
class Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magento_giftregistry_form');
        $this->setTitle(__('Gift Registry'));
    }

    /**
     * Prepare edit form
     *
     * @return Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
