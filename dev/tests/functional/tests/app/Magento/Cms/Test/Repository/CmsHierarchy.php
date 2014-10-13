<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CmsHierarchy
 * Cms Hierarchy repository
 */
class CmsHierarchy extends AbstractRepository
{
    /**
     * @construct
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['cmsHierarchy'] = [
            'identifier' => 'Node_%isolation%',
            'label' => 'node_%isolation%',
            'menu_brief' => 1,
            'nodes_data' => [
                '_0' => [
                    'node_id' => '_0',
                    'parent_node_id' => '',
                    'page_id' => '',
                    'label' => 'Node_%isolation%',
                    'identifier' => 'Node_%isolation%',
                    'sort_order' => 0,
                    'level' => 1,
                    'meta_first_last' => '0',
                    'meta_next_previous' => '0',
                    'meta_cs_enabled' => '0',
                    'meta_chapter_section' => '',
                    'pager_visibility' => '2',
                    'pager_frame' => '',
                    'pager_jump' => '',
                    'menu_visibility' => '0',
                    'menu_brief' => '1',
                    'menu_layout' => '',
                    'menu_excluded' => '0',
                    'menu_levels_down' => '',
                    'menu_ordered' => '0',
                    'menu_list_type' => '',
                    'top_menu_visibility' => '1',
                    'top_menu_excluded' => '0',
                ],
                '_1' => [
                    'node_id' => '_1',
                    'parent_node_id' => '_0',
                    'page_id' => '1',
                    'label' => '404 Not Found 1',
                    'identifier' => 'no-route',
                    'sort_order' => 0,
                    'level' => 2,
                ],
            ],
            'pager_visibility' => 2,
            'top_menu_visibility' => 1,
        ];
    }
}
