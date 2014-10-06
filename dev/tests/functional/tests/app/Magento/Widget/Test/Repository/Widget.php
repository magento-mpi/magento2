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
            'parameters' => [
                'display_mode' => 'catalogrule'
            ],
            'theme_id' => '2'
        ];

        $this->_data['cms_page_link'] = [
            'code' => 'CMS Page Link',
            'title' => 'Cms Page Link %isolation%',
            'store_ids' => ['dataSet' => 'All Store Views'],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'all_pages',
                    'all_pages' => [
                        'layout_handle' => 'default',
                        'for' => 'all',
                        'block' => 'content',
                        'template' => 'widget/link/link_block.phtml'
                    ]
                ]
            ],
            'parameters' => [
                'display_mode' => 'fixed',
                'anchor_text' => 'text',
                'title' => 'anchor title',

            ],
            'page_id' => ['dataSet' => 'default'],
            'theme_id' => 'Magento Blank'
        ];
    }
}
