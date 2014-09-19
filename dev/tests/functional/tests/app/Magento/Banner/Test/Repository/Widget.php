<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Repository;

use  Magento\Widget\Test\Repository\Widget as ParentWidget;

/**
 * Class Widget Repository
 */
class Widget extends ParentWidget
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['banner_rotator_non_anchor_categories'] = [
            'code' => 'magento_banner',
            'title' => 'Banner Rotator %isolation%',
            'store_ids' => [
                '0' => 'All Store Views'
            ],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'notanchor_categories',
                    'notanchor_categories' => [
                        'layout_handle' => 'default',
                        'for' => 'all',
                        'block' => 'content',
                        'template' => 'widget/block.phtml'
                    ]
                ]
            ],
            'parameters' => [
                'display_mode' => 'fixed'
            ],
            'theme_id' => 'Magento Blank'
        ];

        $this->_data['banner_rotator_shoping_cart'] = [
            'code' => 'magento_banner',
            'title' => 'Banner Rotator %isolation%',
            'store_ids' => [
                '0' => 'All Store Views'
            ],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'pages',
                    'pages' => [
                        'layout_handle' => 'checkout_cart_index',
                        'for' => 'all',
                        'block' => 'content',
                        'template' => 'widget/block.phtml'
                    ]
                ]
            ],
            'parameters' => [
                'display_mode' => 'fixed'
            ],
            'theme_id' => 'Magento Blank'
        ];

        $this->_data['banner_rotator'] = [
            'code' => 'magento_banner',
            'title' => 'Banner Rotator %isolation%',
            'store_ids' => [
                '0' => 'All Store Views'
            ],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'all_pages',
                    'all_pages' => [
                        'layout_handle' => 'default',
                        'for' => 'all',
                        'block' => 'content',
                        'template' => 'widget/block.phtml'
                    ]
                ]
            ],
            //TODO 'parameters' array should be deleted while creating functional test for widget (MTA-296)
            'parameters' => [
                'display_mode' => 'catalogrule'
            ],
            //TODO 'theme_id' should be specified via UI and data source should be used
            'theme_id' => 'Magento Blank'
        ];

        $this->_data['widget_banner_rotator'] = [
            'code' => 'Banner Rotator',
            'theme_id' => 'Magento Blank',
        ];
    }
}
