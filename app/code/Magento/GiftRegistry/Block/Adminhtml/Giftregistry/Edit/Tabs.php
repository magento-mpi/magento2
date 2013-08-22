<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tabs
    extends Magento_Adminhtml_Block_Widget_Tabs
{
    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magento_giftregistry_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gift Registry'));
    }

    /**
     * Add tab sections
     *
     * @return Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'   => __('General Information'),
            'content' => $this->getLayout()->createBlock(
                'Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_General'
            )->toHtml()
        ));

        $this->addTab('registry_attributes', array(
            'label'   => __('Attributes'),
            'content' => $this->getLayout()->createBlock(
                'Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_Registry'
            )->toHtml()
        ));

        return parent::_beforeToHtml();
    }

}
