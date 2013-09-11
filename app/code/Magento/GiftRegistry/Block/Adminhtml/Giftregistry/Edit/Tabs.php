<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit;

class Tabs
    extends \Magento\Adminhtml\Block\Widget\Tabs
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
     * @return \Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'   => __('General Information'),
            'content' => $this->getLayout()->createBlock(
                '\Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab\General'
            )->toHtml()
        ));

        $this->addTab('registry_attributes', array(
            'label'   => __('Attributes'),
            'content' => $this->getLayout()->createBlock(
                '\Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab\Registry'
            )->toHtml()
        ));

        return parent::_beforeToHtml();
    }

}
