<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Widget Repository
 */
class Widget extends AbstractRepository
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
        $this->_data['default'] = [
            'title' => 'Test Frontend App',
            'store_ids' => [
                '0' => '0'
            ],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'all_pages',
                    'all_pages' => [
                        'page_id' => '0',
                        'layout_handle' => 'default',
                        'for' => 'all',
                        'block' => 'content',
                        'template' => 'widget/block.phtml'
                    ]
                ]
            ],
            'parameters' => [
                'display_mode' => 'catalogrule'
            ],
            'theme_id' => '2'
        ];

        $this->_data['banner_rotator'] = [
            'code' => 'magento_banner',
            'title' => 'Banner Rotator %isolation%',
            'store_ids' => [
                '0' => '0'
            ],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'all_pages',
                    'all_pages' => [
                        'page_id' => '0',
                        'layout_handle' => 'default',
                        'for' => 'all',
                        'block' => 'content',
                        'template' => 'widget/block.phtml'
                    ]
                ]
            ],
            'parameters' => [
                'display_mode' => 'catalogrule'
            ],
            'theme_id' => '2'
        ];
    }
}
