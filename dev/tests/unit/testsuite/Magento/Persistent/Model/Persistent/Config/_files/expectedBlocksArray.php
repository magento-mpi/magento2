<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'reorder_sidebar' => array(
        'name_in_layout' => 'sale.reorder.sidebar',
        'class' => 'Magento_PersistentHistory_Model_Observer',
        'method' => 'initReorderSidebar',
        'block_type' => 'Magento_Sales_Block_Reorder_Sidebar'
    ),
    'viewed_products' => array(
        'name_in_layout' => 'left.reports.product.viewed',
        'class' => 'Magento_PersistentHistory_Model_Observer',
        'method' => 'emulateViewedProductsBlock',
        'block_type' => 'Magento_Sales_Block_Reorder_Sidebar'
    ),
    'compared_products' => array(
        'name_in_layout' => 'right.reports.product.compared',
        'class' => 'Magento_PersistentHistory_Model_Observer',
        'method' => 'emulateComparedProductsBlock',
        'block_type' => 'Magento_Reports_Block_Product_Compared'
    ),
);

