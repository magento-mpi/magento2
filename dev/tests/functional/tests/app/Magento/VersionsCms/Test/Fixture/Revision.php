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
 * Class Revision
 * Cms Page Revision Fixture
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Revision extends InjectableFixture
{
    protected $defaultDataSet = [
        'content' => 'Default Content',
        'user_id' => 'admin',
    ];

    protected $revision_id = [
        'attribute_code' => 'revision_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $version_id = [
        'attribute_code' => 'version_id',
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

    protected $root_template = [
        'attribute_code' => 'root_template',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'design',
    ];

    protected $meta_keywords = [
        'attribute_code' => 'meta_keywords',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'meta-data',
    ];

    protected $meta_description = [
        'attribute_code' => 'meta_description',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'meta-data',
    ];

    protected $content_heading = [
        'attribute_code' => 'content_heading',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'content',
    ];

    protected $content = [
        'attribute_code' => 'content',
        'backend_type' => 'mediumtext',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'content',
    ];

    protected $created_at = [
        'attribute_code' => 'created_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => 'CURRENT_TIMESTAMP',
        'input' => '',
    ];

    protected $layout_update_xml = [
        'attribute_code' => 'layout_update_xml',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'design'
    ];

    protected $custom_theme = [
        'attribute_code' => 'custom_theme',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $custom_root_template = [
        'attribute_code' => 'custom_root_template',
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
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $custom_theme_to = [
        'attribute_code' => 'custom_theme_to',
        'backend_type' => 'date',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $user_id = [
        'attribute_code' => 'user_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $revision_number = [
        'attribute_code' => 'revision_number',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    public function getRevisionId()
    {
        return $this->getData('revision_id');
    }

    public function getVersionId()
    {
        return $this->getData('version_id');
    }

    public function getPageId()
    {
        return $this->getData('page_id');
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

    public function getContentHeading()
    {
        return $this->getData('content_heading');
    }

    public function getContent()
    {
        return $this->getData('content');
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
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

    public function getUserId()
    {
        return $this->getData('user_id');
    }

    public function getRevisionNumber()
    {
        return $this->getData('revision_number');
    }
}
