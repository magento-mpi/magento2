<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
            'label' => 'node_%isolation%',
            'menu_brief' => 'Yes',
            'nodes_data' => ['preset' => 'nodeWithOnePage'],
            'top_menu_visibility' => 'Yes',
            'pager_visibility' => 'Yes',
        ];
    }
}
