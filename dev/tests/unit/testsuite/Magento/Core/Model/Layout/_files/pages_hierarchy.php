<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
?>
<?php
return array(
    'print' => array(
        'name'     => 'print',
        'label'    => 'All Pages (Print Version)',
        'type'     => 'page',
        'children' => array(
            'sales_order_printorder' => array(
                'name'     => 'sales_order_printorder',
                'label'    => 'Sales Order Print View',
                'type'     => 'page',
                'children' => array(),
            ),
            'sales_guest_print' => array(
                'name'     => 'sales_guest_print',
                'label'    => 'Sales Order Print View (Guest)',
                'type'     => 'page',
                'children' => array(),
            ),
        ),
    ),
    'default' => array(
        'name'     => 'default',
        'label'    => 'All Pages',
        'type'     => 'page',
        'children' => array(
            'catalog_category_default' => array(
                'name'     => 'catalog_category_default',
                'label'    => 'Catalog Category (Non-Anchor)',
                'type'     => 'page',
                'children' => array(
                    'catalog_category_layered' => array(
                        'name'     => 'catalog_category_layered',
                        'label'    => 'Catalog Category (Anchor)',
                        'type'     => 'page',
                        'children' => array(),
                    ),
                    'catalog_product_view' => array(
                        'name'     => 'catalog_product_view',
                        'label'    => 'Catalog Product View (Any)',
                        'type'     => 'page',
                        'children' => array(
                            'catalog_product_view_type_simple' => array(
                                'name'     => 'catalog_product_view_type_simple',
                                'label'    => 'Catalog Product View (Simple)',
                                'type'     => 'page',
                                'children' => array(),
                            ),
                            'catalog_product_view_type_configurable' => array(
                                'name'     => 'catalog_product_view_type_configurable',
                                'label'    => 'Catalog Product View (Configurable)',
                                'type'     => 'page',
                                'children' => array(),
                            ),
                            'catalog_product_view_type_grouped' => array(
                                'name'     => 'catalog_product_view_type_grouped',
                                'label'    => 'Catalog Product View (Grouped)',
                                'type'     => 'page',
                                'children' => array(),
                            ),
                        ),
                    ),
                ),
            ),
            'catalogsearch_ajax_suggest' => array(
                'name'     => 'catalogsearch_ajax_suggest',
                'label'    => 'Catalog Quick Search Form Suggestions',
                'type'     => 'fragment',
                'children' => array(),
            ),
            'checkout_onepage_index' => array(
                'name' => 'checkout_onepage_index',
                'label' => 'One Page Checkout',
                'type' => 'page',
                'children' => array(
                    'checkout_onepage_progress' => array(
                        'name' => 'checkout_onepage_progress',
                        'label' => 'One Page Checkout Progress',
                        'type' => 'fragment',
                        'children' => array()
                    ),
                ),
            ),
        ),
    ),
);
