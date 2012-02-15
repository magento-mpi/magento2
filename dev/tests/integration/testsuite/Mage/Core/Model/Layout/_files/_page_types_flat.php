<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
?>
<?php
return array(
    'print' => array(
        'name'   => 'print',
        'label'  => 'All Pages (Print Version)',
        'parent' => null,
    ),
    'sales_order_print' => array(
        'name'   => 'sales_order_print',
        'label'  => 'Sales Order Print View',
        'parent' => 'print',
    ),
    'sales_guest_print' => array(
        'name'   => 'sales_guest_print',
        'label'  => 'Sales Order Print View (Guest)',
        'parent' => 'print',
    ),
    'default' => array(
        'name'   => 'default',
        'label'  => 'All Pages',
        'parent' => null,
    ),
    'catalog_category_default' => array(
        'name'   => 'catalog_category_default',
        'label'  => 'Catalog Category (Non-Anchor)',
        'parent' => 'default',
    ),
    'catalog_category_layered' => array(
        'name'   => 'catalog_category_layered',
        'label'  => 'Catalog Category (Anchor)',
        'parent' => 'catalog_category_default',
    ),
    'catalog_product_view' => array(
        'name'   => 'catalog_product_view',
        'label'  => 'Catalog Product View (Any)',
        'parent' => 'catalog_category_default',
    ),
    'PRODUCT_TYPE_simple' => array(
        'name'   => 'PRODUCT_TYPE_simple',
        'label'  => 'Catalog Product View (Simple)',
        'parent' => 'catalog_product_view',
    ),
    'PRODUCT_TYPE_configurable' => array(
        'name'   => 'PRODUCT_TYPE_configurable',
        'label'  => 'Catalog Product View (Configurable)',
        'parent' => 'catalog_product_view',
    ),
    'PRODUCT_TYPE_grouped' => array(
        'name'   => 'PRODUCT_TYPE_grouped',
        'label'  => 'Catalog Product View (Grouped)',
        'parent' => 'catalog_product_view',
    ),
);
