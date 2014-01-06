<?php
/**
 * Grouped product edit tab
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Block\Adminhtml\Product\Edit\Tabs;

class Grouped extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('super', array(
            'label'     => __('Associated Products'),
            'url'       => $this->getUrl('catalog/*/superGroup', array('_current'=>true)),
            'class'     => 'ajax',
        ));
    }
}
