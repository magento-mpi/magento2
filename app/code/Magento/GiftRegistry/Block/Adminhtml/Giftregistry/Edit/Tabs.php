<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize form
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
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'general_section',
            [
                'label' => __('General Information'),
                'content' => $this->getLayout()->createBlock(
                    'Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab\General'
                )->toHtml()
            ]
        );

        $this->addTab(
            'registry_attributes',
            [
                'label' => __('Attributes'),
                'content' => $this->getLayout()->createBlock(
                    'Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab\Registry'
                )->toHtml()
            ]
        );

        return parent::_beforeToHtml();
    }
}
