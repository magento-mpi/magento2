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
        'name'     => 'print',
        'label'    => 'All Pages (Print Version)',
        'children' => array(
            'sales_order_print' => array(
                'name'     => 'sales_order_print',
                'label'    => 'Sales Order Print View',
                'children' => array(),
            ),
            'sales_guest_print' => array(
                'name'     => 'sales_guest_print',
                'label'    => 'Sales Order Print View (Guest)',
                'children' => array(),
            ),
        ),
    ),
    'default' => array(
        'name'     => 'default',
        'label'    => 'All Pages',
        'children' => array(
            'catalog_category_default' => array(
                'name'     => 'catalog_category_default',
                'label'    => 'Catalog Category (Non-Anchor)',
                'children' => array(
                    'catalog_category_layered' => array(
                        'name'     => 'catalog_category_layered',
                        'label'    => 'Catalog Category (Anchor)',
                        'children' => array(),
                    ),
                    'catalog_product_view' => array(
                        'name'     => 'catalog_product_view',
                        'label'    => 'Catalog Product View (Any)',
                        'children' => array(
                            'PRODUCT_TYPE_simple' => array(
                                'name'     => 'PRODUCT_TYPE_simple',
                                'label'    => 'Catalog Product View (Simple)',
                                'children' => array(),
                            ),
                            'PRODUCT_TYPE_configurable' => array(
                                'name'     => 'PRODUCT_TYPE_configurable',
                                'label'    => 'Catalog Product View (Configurable)',
                                'children' => array(),
                            ),
                            'PRODUCT_TYPE_grouped' => array(
                                'name'     => 'PRODUCT_TYPE_grouped',
                                'label'    => 'Catalog Product View (Grouped)',
                                'children' => array(),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
