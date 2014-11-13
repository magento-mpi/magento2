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
            'code' => 'Banner Rotator',
            'title' => 'Banner Rotator %isolation%',
            'store_ids' => ['dataSet' => 'All Store Views'],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'notanchor_categories',
                    'notanchor_categories' => [
                        'block' => 'Main Content Area',
                        'template' => 'Banner Block Template'
                    ]
                ]
            ],
            'parameters' => [
                'display_mode' => 'fixed'
            ],
            'theme_id' => 'Magento Blank'
        ];

        $this->_data['banner_rotator_shoping_cart'] = [
            'code' => 'Banner Rotator',
            'title' => 'Banner Rotator %isolation%',
            'store_ids' => [
                '0' => 'All Store Views'
            ],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'pages',
                    'pages' => [
                        'block' => 'Main Content Area',
                        'template' => 'Banner Block Template'
                    ]
                ]
            ],
            'parameters' => [
                'display_mode' => 'fixed'
            ],
            'theme_id' => 'Magento Blank'
        ];

        $this->_data['banner_rotator'] = [
            'code' => 'Banner Rotator',
            'title' => 'Banner Rotator %isolation%',
            'store_ids' => ['dataSet' => 'All Store Views'],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'All Pages',
                    'all_pages' => [
                        'block' => 'Main Content Area',
                        'template' => 'Banner Block Template'
                    ]
                ]
            ],
            //TODO 'parameters' array should be deleted while creating functional test for widget (MTA-296)
            'parameters' => [
                'display_mode' => 'catalogrule'
            ],
            'theme_id' => 'Magento Blank'
        ];

        $this->_data['widget_banner_rotator'] = [
            'code' => 'Banner Rotator',
            'theme_id' => 'Magento Blank',
        ];
    }
}
