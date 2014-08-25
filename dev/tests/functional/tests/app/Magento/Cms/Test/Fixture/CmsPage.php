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
 * CMS Page fixture
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
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
        'title' => 'CMS Page%isolation%',
        'identifier' => 'identifier%isolation%',
        'store_id' => 'All Store Views',
        'is_active' => 'Published',
        'under_version_control' => 'No',
        'content' => [
            'content' => 'Text %isolation%']
    ];

    protected $page_id = [
        'attribute_code' => 'page_id',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $title = [
        'attribute_code' => 'title',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'page_information',
        'selector' => '#page_title'
    ];

    protected $page_layout = [
        'attribute_code' => 'page_layout',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $meta_keywords = [
        'attribute_code' => 'meta_keywords',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'group' => 'meta_data',
        'selector' => '#page_meta_keywords'
    ];

    protected $meta_description = [
        'attribute_code' => 'meta_description',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'group' => 'meta_data',
        'selector' => '#page_meta_description'
    ];

    protected $identifier = [
        'attribute_code' => 'identifier',
        'backend_type' => 'varchar',
        'group' => 'page_information',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $content_heading = [
        'attribute_code' => 'content_heading',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'group' => 'content',
        'input' => '',
    ];

    protected $content = [
        'attribute_code' => 'content',
        'backend_type' => 'mediumtext',
        'is_required' => '',
        'default_value' => '',
        'group' => 'content',
        'input' => '',
        'source' => 'Magento\Cms\Test\Fixture\CmsPage\Content',
    ];

    protected $creation_time = [
        'attribute_code' => 'creation_time',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $update_time = [
        'attribute_code' => 'update_time',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
        'group' => 'page_information',
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $layout_update_xml = [
        'attribute_code' => 'layout_update_xml',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $custom_theme = [
        'attribute_code' => 'custom_theme',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $custom_page_layout = [
        'attribute_code' => 'custom_page_layout',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $custom_layout_update_xml = [
        'attribute_code' => 'custom_layout_update_xml',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $custom_theme_from = [
        'attribute_code' => 'custom_theme_from',
        'backend_type' => 'date',
        'source' => 'Magento\Backend\Test\Fixture\Date',
    ];

    protected $custom_theme_to = [
        'attribute_code' => 'custom_theme_to',
        'backend_type' => 'date',
        'source' => 'Magento\Backend\Test\Fixture\Date',
    ];

    protected $published_revision_id = [
        'attribute_code' => 'published_revision_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $website_root = [
        'attribute_code' => 'website_root',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
    ];

    protected $under_version_control = [
        'attribute_code' => 'under_version_control',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'group' => 'page_information',
        'input' => '',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'virtual',
        'is_required' => '1',
        'default_value' => '0',
        'group' => 'page_information',
        'input' => 'multiselect',
    ];

    public function getPageId()
    {
        return $this->getData('page_id');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function getPageLayout()
    {
        return $this->getData('page_layout');
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

    public function getCustomPageLayout()
    {
        return $this->getData('custom_page_layout');
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
