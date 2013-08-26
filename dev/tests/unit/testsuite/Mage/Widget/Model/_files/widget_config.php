<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'sales_widget_guestform' => array(
        '@' => array(
            'type' => 'Mage_Sales_Block_Widget_Guest_Form',
            'module' => "Mage_Sales",
            'translate' => 'label description',
        ),
        'is_email_compatible' => '1',
        'name' => 'Orders and Returns',
        'description' => 'Orders and Returns Search Form',
        'parameters' => array(
            'title' => array(
                'type' => 'text',
                'visible' => '0',
                '@' => array(
                    'translate' => 'label',
                ),
                'label' => 'Anchor Custom Title'
            ),
            'template' => array(
                'type' => 'select',
                'value' => 'hierarchy/widget/link/link_block.phtml',
                'values' => array(
                    'default' => array(
                        '@' => array(
                            'translate' => 'label',
                        ),
                        'value' => 'hierarchy/widget/link/link_block.phtml',
                        'label' => 'CMS Page Link Block Template',
                    ),
                    'link_inline' => array(
                        '@' => array(
                            'translate' => 'label',
                        ),
                        'value' => 'hierarchy/widget/link/link_inline.phtml',
                        'label' => 'CMS Page Link Inline Template'
                    ),
                ),
                'visible' => '0'
            ),
            'link_display' => array(
                'source_model' => 'Mage_Backend_Model_Config_Source_Yesno',
                'type' => 'select',
                'visible' => '1',
                '@' => array(
                    'translate' => 'label_description',
                ),
                'sort_order' => '10',
                'label' => 'Display a Link to Loading a Spreadsheet',
                'description' => "Defines whether a link to My Account",
            ),
            'link_text' => array(
                'type' => 'text',
                'visible' => '1',
                'required' => '1',
                '@' => array(
                    'translate' => 'label description value',
                ),
                'sort_order' => '20',
                'label' => 'Link Text',
                'description' => 'The text of the link to the My Account &gt; Order by SKU page',
                'depends' => array(
                    'link_display' => array(
                        'value' => '1',
                    ),
                ),
                'value' => 'Load a list of SKUs',
            ),
            'id_path' => array(
                'type' => 'label',
                '@' => array(
                    'type' => 'complex',
                    'translate' => 'label'
                ),
                'helper_block' => array(
                    'type' => 'Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser',
                    'data' => array(
                        'button' => array(
                            '@' => array(
                                'translate' => 'open',
                            ),
                            'open' => 'Select Product...'
                        )
                    )
                ),
                'visible' => '1',
                'required' => '1',
                'sort_order' => '10',
                'label' => 'Product',
            ),
        ),
        'supported_containers' => array(
            'left_column' => array(
                'container_name' => 'left',
                'template' => array(
                    'default' => 'default_template',
                )
            ),
            'right_column' => array(
                'container_name' => 'right',
                'template' => array(
                    'default' => 'default_template',
                )
            ),
        ),
    )
);

