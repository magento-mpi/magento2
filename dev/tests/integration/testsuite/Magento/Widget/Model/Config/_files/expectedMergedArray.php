<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'new_products' => array(
        '@' => array(
            'type' => 'Magento_Sales_Block_Widget_Guest_Form',
        ),
        'is_email_compatible' => '1',
        'placeholder_image' => 'Magento_Catalog::images/product_widget_new.gif',
        'name' => 'Orders and Returns',
        'description' => 'Orders and Returns Search Form',
        'parameters' => array(
            'display_type' => array(
                'type' => 'select',
                'value' => 'all_products',
                'values' => array(
                    'default' => array(
                        'value' => 'all_products',
                        'label' => 'All products',
                    ),
                    'item' => array(
                        'value' => 'new_products',
                        'label' => 'New products',
                    ),
                ),
                'visible' => '1',
                'required' => '1',
                'label' => 'Display Type',
                'description' => 'All products - recently added products, New products - products marked as new',
            ),
            'show_pager' => array(
                'source_model' => "Magento_Backend_Model_Config_Source_Yesno",
                'type' => 'select',
                'visible' => '1',
                'label' => 'Display Page Control',
            ),
            'products_per_page' => array(
                'type' => 'text',
                'value' => '5',
                'visible' => '1',
                'required' => '1',
                'label' => 'Number of Products per Page',
                'depends' => array(
                    'show_pager' => array(
                        'value' => '1'
                    )
                )
            ),
            'products_count' => array(
                'type' => 'text',
                'value' => '10',
                'visible' => '1',
                'required' => '1',
                'label' => 'Number of Products to Display',
            ),
            'template' => array(
                'type' => 'select',
                'value' => 'product/widget/new/content/new_grid.phtml',
                'values' => array(
                    'default' => array(
                        'value' => 'product/widget/new/content/new_grid.phtml',
                        'label' => 'New Products Grid Template',
                    ),
                    'list' => array(
                        'value' => 'product/widget/new/content/new_list.phtml',
                        'label' => 'New Products List Template',
                    ),
                    'list_default' => array(
                        'value' => 'product/widget/new/column/new_default_list.phtml',
                        'label' => 'New Products Images and Names Template',
                    ),
                    'list_names' => array(
                        'value' => 'product/widget/new/column/new_names_list.phtml',
                        'label' => 'New Products Names Only Template',
                    ),
                    'list_images' => array(
                        'value' => 'product/widget/new/column/new_images_list.phtml',
                        'label' => 'New Products Images Only Template',
                    ),
                    'default_template' => array(
                        'value' => 'widget/guest/form.phtml',
                        'label' => 'Default Template'
                    ),
                ),
                'visible' => '0',
                'required' => '1',
                'label' => 'Template'
            ),
            'cache_lifetime' => array(
                'type' => 'text',
                'visible' => '1',
                'label' => 'Cache Lifetime (Seconds)',
                'description' => "86400 by default, if not set. To refresh instantly, clear the Blocks HTML
                    Output cache.
                ",
            ),
            'title' => array(
                'type' => 'text',
                'visible' => '0',
                'label' => 'Anchor Custom Title',
            ),
        ),
        'supported_containers' => array(
            '0' => array(
                'container_name' => 'left',
                'template' => array(
                    'default' => 'default_template',
                    'names_only' => 'list_names',
                    'images_only' => 'list_images'
                )
            ),
            '1' => array(
                'container_name' => 'content',
                'template' => array(
                    'grid' => 'default',
                    'list' => 'list'
                )
            ),
            '2' => array(
                'container_name' => 'right',
                'template' => array(
                    'default' => 'default_template',
                    'names_only' => 'list_names',
                    'images_only' => 'list_images'
                )
            ),
        ),
    )
);

