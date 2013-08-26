<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'cms_page_link' => array(
        '@' => array(
            'type' => 'Mage_Cms_Block_Widget_Page_Link',
            'module' => 'Mage_Cms',
            'translate' => 'label description',
        ),
        'name' => 'CMS Page Link',
        'description' => 'Link to a CMS Page',
        'is_email_compatible' => 'true',
        'placeholder_image' => 'Mage_Cms::images/widget_page_link.gif',
        'parameters' => array(
            'page_id' => array(
                '@' => array(
                    'type' => 'complex',
                    'translate' => 'label',
                ),
                'type' => 'label',
                'helper_block' => array(
                    'type' => 'Mage_Adminhtml_Block_Cms_Page_Widget_Chooser',
                    'data' => array(
                        'button' => array(
                            '@' => array(
                                'translate' => 'open',
                            ),
                            'open' => 'Select Page...',
                        ),
                    ),
                ),
                'visible' => 'true',
                'required' => 'true',
                'sort_order' => '10',
                'label' => 'CMS Page',
            ),
            'anchor_text' => array(
                '@' => array(
                    'translate' => 'label description',
                ),
                'type' => 'text',
                'visible' => 'true',
                'label' => 'Anchor Custom Text',
                'description' => 'If empty, the Page Title will be used',
                'depends' => array(
                    'show_pager' => array(
                        'value' => 'true',
                    ),
                ),
            ),
            'template' => array(
                '@' => array(
                    'translate' => 'label',
                ),
                'type' => 'select',
                'values' => array(
                    'default' => array(
                        '@' => array(
                            'translate' => 'label',
                        ),
                        'value' => 'product/widget/link/link_block.phtml',
                        'label' => 'Product Link Block Template',
                    ),
                    'link_inline' => array(
                        '@' => array(
                            'translate' => 'label',
                        ),
                        'value' => 'product/widget/link/link_inline.phtml',
                        'label' => 'Product Link Inline Template',
                    )
                ),
                'visible' => 'true',
                'label' => 'Template',
                'value' => 'product/widget/link/link_block.phtml',
            ),
        ),
        'supported_containers' => array(
            'left_column' => array(
                'container_name' => 'left',
                'template' => array(
                    'default' => 'list_default',
                    'names_only' => 'list_names',
                ),
            ),
            'main_content' => array(
                'container_name' => 'content',
                'template' => array(
                    'grid' => 'default',
                    'list' => 'list',
                ),
            )
        )
    ),
    'enterprise_giftregistry_search' => array(
        '@' => array(
            'type' => 'Enterprise_GiftRegistry_Block_Search_Widget_Form',
            'module' => 'Enterprise_GiftRegistry',
            'translate' => 'label description',
        ),
        'name' => 'Gift Registry Search',
        'description' => 'Gift Registry Quick Search Form',
        'parameters' => array(
            'types' => array(
                'type' => 'multiselect',
                'visible' => 'true', // verify default value is injected
                'source_model' => 'Enterprise_GiftRegistry_Model_Source_Search',
            ),
        ),
    ),

);