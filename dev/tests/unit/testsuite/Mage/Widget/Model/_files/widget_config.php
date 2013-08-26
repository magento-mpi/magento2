<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'widgets' => array(
        'sales_widget_guestform' => array(
            '@' => array(
                'type' => 'Mage_Sales_Block_Widget_Guest_Form',
                'translate' => 'label description',
                'module' => "Mage_Sales",
            ),
            'is_email_compatible' => 'true',
            'name' => 'Orders and Returns',
            'description' => 'Orders and Returns Search Form',
            'parameters' => array(
                'title' => array(
                    'type' => 'text',
                    'visible' => 'false',
                    '@' => array(
                        'translate' => 'label',
                    ),
                    'label' => 'Anchor Custom Title'
                ),
                'template' => array(
                    'type' => 'select',
                    'values' => array(
                        'default' => array(
                            '@' => array(
                                'translate' => 'label',
                                'value' => 'hierarchy/widget/link/link_block.phtml',
                            ),
                            'label' => 'CMS Page Link Block Template',
                        ),
                        'link_inline' => array(
                            '@' => array(
                                'translate' => 'label',
                                'value' => 'hierarchy/widget/link/link_inline.phtml',
                            ),
                            'label' => 'CMS Page Link Inline Template'
                        ),
                    ),
                    'value' => 'hierarchy/widget/link/link_block.phtml',
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
                    'value' => 'Load a list of SKUs',
                    'sort_order' => '20',
                    'label' => 'Link Text,',
                    'description' => 'The text of the link to the My Account &gt; Order by SKU page',
                    'depends' => array(
                        'link_display' => array(
                            'value' => '1',
                        ),
                    ),
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
    )
);

