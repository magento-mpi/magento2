<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Cms Hierarchy fixture
 */
class CmsHierarchy extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\VersionsCms\Test\Repository\CmsHierarchy';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\VersionsCms\Test\Handler\CmsHierarchy\CmsHierarchyInterface';

    protected $defaultDataSet = [
        'identifier' => 'Node_%isolation%',
        'label' => 'node_%isolation%',
        'menu_brief' => 'Yes',
        'nodes_data' => ['preset' => 'nodeWithOnePage'],
        'top_menu_visibility' => 'Yes',
        'pager_visibility' => 'Yes',
        'meta_cs_enabled' => 'Yes',
        'meta_first_last' => 'Yes',
        'meta_next_previous' => 'Yes',
        'meta_chapter_section' => 'Both',
    ];

    protected $node_id = [
        'attribute_code' => 'node_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $parent_node_id = [
        'attribute_code' => 'parent_node_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $page_id = [
        'attribute_code' => 'page_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $identifier = [
        'attribute_code' => 'identifier',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $label = [
        'attribute_code' => 'label',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $level = [
        'attribute_code' => 'level',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $request_url = [
        'attribute_code' => 'request_url',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $xpath = [
        'attribute_code' => 'xpath',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $scope = [
        'attribute_code' => 'scope',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'default',
        'input' => '',
    ];

    protected $scope_id = [
        'attribute_code' => 'scope_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $pager_visibility = [
        'attribute_code' => 'pager_visibility',
        'backend_type' => 'virtual',
    ];

    protected $top_menu_visibility = [
        'attribute_code' => 'top_menu_visibility',
        'backend_type' => 'virtual',
    ];

    protected $menu_brief = [
        'attribute_code' => 'menu_brief',
        'backend_type' => 'virtual',
    ];

    protected $nodes_data = [
        'attribute_code' => 'nodes_data',
        'backend_type' => 'virtual',
        'source' => 'Magento\VersionsCms\Test\Fixture\CmsHierarchy\Nodes',
    ];

    protected $meta_cs_enabled = [
        'attribute_code' => 'meta_cs_enabled',
        'backend_type' => 'virtual',
    ];

    protected $meta_first_last = [
        'attribute_code' => 'meta_first_last',
        'backend_type' => 'virtual',
    ];

    protected $meta_next_previous = [
        'attribute_code' => 'meta_next_previous',
        'backend_type' => 'virtual',
    ];

    protected $meta_chapter_section = [
        'attribute_code' => 'meta_chapter_section',
        'backend_type' => 'virtual',
    ];

    public function getNodeId()
    {
        return $this->getData('node_id');
    }

    public function getParentNodeId()
    {
        return $this->getData('parent_node_id');
    }

    public function getPageId()
    {
        return $this->getData('page_id');
    }

    public function getIdentifier()
    {
        return $this->getData('identifier');
    }

    public function getLabel()
    {
        return $this->getData('label');
    }

    public function getLevel()
    {
        return $this->getData('level');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function getRequestUrl()
    {
        return $this->getData('request_url');
    }

    public function getXpath()
    {
        return $this->getData('xpath');
    }

    public function getScope()
    {
        return $this->getData('scope');
    }

    public function getScopeId()
    {
        return $this->getData('scope_id');
    }

    public function getPagerVisibility()
    {
        return $this->getData('pager_visibility');
    }

    public function getTopMenuVisibility()
    {
        return $this->getData('top_menu_visibility');
    }

    public function getMenuBrief()
    {
        return $this->getData('menu_brief');
    }

    public function getNodesData()
    {
        return $this->getData('nodes_data');
    }

    public function getMetaCsEnabled()
    {
        return $this->getData('meta_cs_enabled');
    }

    public function getMetaFirstLast()
    {
        return $this->getData('meta_first_last');
    }

    public function getMetaNextPrevious()
    {
        return $this->getData('meta_next_previous');
    }

    public function getMetaChapterSection()
    {
        return $this->getData('meta_chapter_section');
    }
}
