<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CmsPage
 *
 * @package Magento\Cms\Test\Fixture
 */
class CmsPage extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Cms\Test\Repository\CmsPage';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Cms\Test\Handler\CmsPage\CmsPageInterface';

    protected $defaultDataSet = [
        'title' => null,
        'identifier' => null,
        'store_id' => null,
        'is_active' => null,
        'under_version_control' => null,
        'content' => null,
    ];

    protected $title = [
        'attribute_code' => 'title',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'CMS Page%isolation%',
        'group' => 'page_tabs_main_section',
        'selector' => '#page_title'
    ];

    protected $identifier = [
        'attribute_code' => 'identifier',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'identifier%isolation%',
        'group' => 'page_tabs_main_section',
        'selector' => '#page_identifier'
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'virtual',
        'is_required' => '1',
        'default_value' => 'All Store Views',
        'input' => 'select',
        'group' => 'page_tabs_main_section',
        'selector' => '#page_store_id'
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => 'Published',
        'input' => 'select',
        'group' => 'page_tabs_main_section',
        'selector' => '#page_is_active'
    ];

    protected $under_version_control = [
        'attribute_code' => 'under_version_control',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => 'No',
        'input' => 'select',
        'group' => 'page_tabs_main_section',
        'selector' => '#page_under_version_control'
    ];

    protected $content = [
        'attribute_code' => 'content',
        'backend_type' => 'mediumtext',
        'is_required' => '',
        'default_value' => 'Test %isolation%',
        'group' => 'page_tabs_content_section',
        'selector' => '#page_content'
    ];

    protected $meta_keywords = [
        'attribute_code' => 'meta_keywords',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'group' => 'page_tabs_meta_section',
        'selector' => '#page_meta_keywords'
    ];

    protected $meta_description = [
        'attribute_code' => 'meta_description',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'group' => 'page_tabs_meta_section',
        'selector' => '#page_meta_description'
    ];

    protected $page_id = [
        'attribute_code' => 'page_id',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => ''
    ];

    protected $root_template = [
        'attribute_code' => 'root_template',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $content_heading = [
        'attribute_code' => 'content_heading',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $creation_time = [
        'attribute_code' => 'creation_time',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $update_time = [
        'attribute_code' => 'update_time',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0'
    ];

    protected $layout_update_xml = [
        'attribute_code' => 'layout_update_xml',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $custom_theme = [
        'attribute_code' => 'custom_theme',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $custom_root_template = [
        'attribute_code' => 'custom_root_template',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $custom_layout_update_xml = [
        'attribute_code' => 'custom_layout_update_xml',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $custom_theme_from = [
        'attribute_code' => 'custom_theme_from',
        'backend_type' => 'date',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $custom_theme_to = [
        'attribute_code' => 'custom_theme_to',
        'backend_type' => 'date',
        'is_required' => '',
        'default_value' => ''
    ];

    protected $published_revision_id = [
        'attribute_code' => 'published_revision_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0'
    ];

    protected $website_root = [
        'attribute_code' => 'website_root',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1'
    ];

    public function getPageId()
    {
        return $this->getData('page_id');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function getRootTemplate()
    {
        return $this->getData('root_template');
    }

    public function getMetaKeywords()
    {
        return $this->getData('meta_keywords');
    }

    public function getMetaDescription()
    {
        return $this->getData('meta_description');
    }

    public function getIdentifier()
    {
        return $this->getData('identifier');
    }

    public function getContentHeading()
    {
        return $this->getData('content_heading');
    }

    public function getContent()
    {
        return $this->getData('content');
    }

    public function getCreationTime()
    {
        return $this->getData('creation_time');
    }

    public function getUpdateTime()
    {
        return $this->getData('update_time');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function getLayoutUpdateXml()
    {
        return $this->getData('layout_update_xml');
    }

    public function getCustomTheme()
    {
        return $this->getData('custom_theme');
    }

    public function getCustomRootTemplate()
    {
        return $this->getData('custom_root_template');
    }

    public function getCustomLayoutUpdateXml()
    {
        return $this->getData('custom_layout_update_xml');
    }

    public function getCustomThemeFrom()
    {
        return $this->getData('custom_theme_from');
    }

    public function getCustomThemeTo()
    {
        return $this->getData('custom_theme_to');
    }

    public function getPublishedRevisionId()
    {
        return $this->getData('published_revision_id');
    }

    public function getWebsiteRoot()
    {
        return $this->getData('website_root');
    }

    public function getUnderVersionControl()
    {
        return $this->getData('under_version_control');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }
}
