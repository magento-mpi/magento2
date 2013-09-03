<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    '@' => array(
        'type' => 'Mage_Cms_Block_Widget_Page_Link',
        'module' => 'Mage_Cms',
        'translate' => 'name description',
    ),
    'name' => 'CMS Page Link',
    'description' => 'Link to a CMS Page',
    'is_email_compatible' => '1',
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
            'visible' => '1',
            'required' => '1',
            'sort_order' => '10',
            'label' => 'CMS Page',
        ),
        'anchor_text' => array(
            '@' => array(
                'translate' => 'label description',
            ),
            'type' => 'text',
            'visible' => '1',
            'label' => 'Anchor Custom Text',
            'description' => 'If empty, the Page Title will be used',
            'depends' => array(
                'show_pager' => array(
                    'value' => '1',
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
            'visible' => '1',
            'label' => 'Template',
            'value' => 'product/widget/link/link_block.phtml',
        ),
    ),
    'supported_containers' => array(
        '0' => array(
            'container_name' => 'left',
            'template' => array(
                'default' => 'default',
                'names_only' => 'link_inline',
            ),
        ),
        '1' => array(
            'container_name' => 'content',
            'template' => array(
                'grid' => 'default',
                'list' => 'list',
            ),
        )
    )
);