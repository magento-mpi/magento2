<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'blocks' => array(
        'reorder_sidebar' => array(
            'name_in_layout' => 'sale.reorder.sidebar',
            'class' => 'Magento\PersistentHistory\Model\Observer',
            'method' => 'initReorderSidebar',
            'block_type' => 'Magento\Sales\Block\Reorder\Sidebar'
        ),
        'viewed_products' => array(
            'name_in_layout' => 'left.reports.product.viewed',
            'class' => 'Magento\PersistentHistory\Model\Observer',
            'method' => 'emulateViewedProductsBlock',
            'block_type' => 'Magento\Sales\Block\Reorder\Sidebar'
        ),
        'compared_products' => array(
            'name_in_layout' => 'right.reports.product.compared',
            'class' => 'Magento\PersistentHistory\Model\Observer',
            'method' => 'emulateComparedProductsBlock',
            'block_type' => 'Magento\Reports\Block\Product\Compared'
        ),
    )
);

