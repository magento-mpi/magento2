<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'widget' => array(
        '0' => array(
            '@' => array(
                'id' => 'sales_widget_guestfrom',
                'class' => 'Mage_Sales_Block_Widget_Guest_Form',
                'translate' => 'label description',
                'module' => "Mage_Sales",
                'is_email_compatible' => 'true',
            ),
            'label' => array(
                '0' => 'Orders and Returns',
            ),
            'description' => array(
                '0' => 'Orders and Returns Search Form'
            ),
            'parameter' => array(
                '0' => array(
                    '@' => array(
                        'name' => 'title',
                        'type' => 'text',
                        'translate' => 'label',
                        'visible' => 'false'
                    ),
                    'label' => array(
                        '0' => 'Anchor Custom Title'
                    ),
                ),
                '1' => array(
                    '@' => array(
                        'name' => 'template',
                        'type' => 'select',
                        'visible' => 'false'
                    ),
                    'option' => array(
                        '0' => array(
                            '@' => array(
                                'name' => 'default',
                                'translate' => 'label',
                                'value' => 'hierarchy/widget/link/link_block.phtml',
                                'selected' => 'true'
                            ),
                            'label' => array(
                                '0' => 'CMS Page Link Block Template'
                            )
                        ),
                        '1' => array(
                            '@' => array(
                                'name' => 'link_inline',
                                'translate' => 'label',
                                'value' => 'hierarchy/widget/link/link_inline.phtml',
                            ),
                            'label' => array(
                                '0' => 'CMS Page Link Inline Template'
                            )
                        ),
                    ),
                ),
                '2' => array(
                    '@' => array(
                        'name' => 'link_display',
                        'type' => 'select',
                        'translate' => 'label_description',
                        'visible' => 'true',
                        'sort_order' => '10',
                        'source_model' => 'Mage_Backend_Model_Config_Source_Yesno'
                    ),
                    'label' => array(
                        '0' => 'Display a Link to Loading a Spreadsheet'
                    ),
                    'description' => array(
                        '0' => 'Defines whether a link to My Account &gt; Order by SKU page will be
                        displayed on the widget'
                    )
                ),
                '3' => array(
                    '@' => array(
                        'name' => 'link_text',
                        'type' => 'text',
                        'translate' => 'label description value',
                        'required' => 'true',
                        'visible' => 'true',
                        'sort_order' => '20',
                        'value' => 'Load a list of SKUs'
                    ),
                    'label' => array(
                        '0' => 'Link Text'
                    ),
                    'description' => array(
                        '0' => 'The text of the link to the My Account &gt; Order by SKU page'
                    ),
                    'depends' => array(
                        '0' => array(
                            'parameter' => array(
                                '0' => array(
                                    '@' => array(
                                        'name' => 'link_display',
                                        'value' => 1
                                    ),
                                    '0' => ''
                                )
                            )
                        )
                    )
                ),
                '4' => array(
                    '@' => array(
                        'name' => 'id_path',
                        'type' => 'value_renderer',
                        'visible' => 'true',
                        'required' => 'true',
                        'sort_order' => '10',
                        'translate' => 'label'
                    ),
                    'label' => array(
                        '0' => 'Product'
                    ),
                    'renderer' => array(
                        '0' => array(
                            '@' => array(
                                'class' => 'Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser'
                            ),
                            'data' => array(
                                '0' => array(
                                    'button' => array(
                                        '0' => array(
                                            '@' => array(
                                                'translate' => 'open'
                                            ),
                                            'open' => array(
                                                '0' => 'Select Product...'
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    ),
                ),
            ),
            'container' => array(
                '0' => array(
                    '@' => array(
                        'section' => 'left_column',
                        'name' => 'left'
                    ),
                    'template' => array(
                        '0' => array(
                            '@' => array(
                                'name' => 'default',
                                'value' => 'default_template'
                            ),
                            '0' => ''
                        )
                    )
                ),
                '1' => array(
                    '@' => array(
                        'section' => 'right_column',
                        'name' => 'right'
                    ),
                    'template' => array(
                        '0' => array(
                            '@' => array(
                                'name' => 'default',
                                'value' => 'default_template'
                            ),
                            '0' => ''
                        )
                    )
                ),
            )
        )
    )
);
