<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Repository;

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
            'label' => 'node-%isolation%',
            'menu_brief' => 'Neighbours and Children',
            'nodes_data' => ['preset' => 'nodeWithOnePage'],
            'top_menu_visibility' => 'No',
            'pager_visibility' => 'Yes',
            'meta_cs_enabled' => 'Yes',
            'meta_first_last' => 'Yes',
            'meta_next_previous' => 'Yes',
            'meta_chapter_section' => 'Both',
            'pager_frame' => 50,
            'menu_visibility' => 'Yes',
            'menu_ordered' => 'Ordered',
        ];
    }
}
