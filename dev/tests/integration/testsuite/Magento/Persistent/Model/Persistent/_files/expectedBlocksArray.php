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
);

