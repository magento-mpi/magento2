<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
return [
    'reorder_sidebar' => [
        'name_in_layout' => 'sale.reorder.sidebar',
        'class' => 'Magento\PersistentHistory\Model\Observer',
        'method' => 'initReorderSidebar',
        'block_type' => 'Magento\Sales\Block\Reorder\Sidebar',
    ],
    'viewed_products' => [
        'name_in_layout' => 'left.reports.product.viewed',
        'class' => 'Magento\PersistentHistory\Model\Observer',
        'method' => 'emulateViewedProductsBlock',
        'block_type' => 'Magento\Sales\Block\Reorder\Sidebar',
    ]
];
