<?php
/**
 * Edit tabs for configurable products
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tabs;

class Configurable extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs
{
    /**
     * Preparing layout
     *
     * @return \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tabs\Configurable
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'super_settings',
            [
                'label' => __('Configurable Product Settings'),
                'content' => $this->getLayout()->createBlock(
                    'Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Settings'
                )->toHtml(),
                'active' => true
            ]
        );
    }
}
