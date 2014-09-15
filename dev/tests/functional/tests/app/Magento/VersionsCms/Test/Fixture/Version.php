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
 * Class Version
 * Cms Page Version Fixture
 */
class Version extends InjectableFixture
{
    protected $defaultDataSet = [
        'label' => 'Version_%isolation%',
        'access_level' => 'Public',
        'user_id' => ['dataSet' => 'admin'],
    ];

    protected $version_id = [
        'attribute_code' => 'version_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $label = [
        'attribute_code' => 'label',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $access_level = [
        'attribute_code' => 'access_level',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
    ];

    protected $page_id = [
        'attribute_code' => 'page_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $user_id = [
        'attribute_code' => 'user_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
        'source' => 'Magento\VersionsCms\Test\Fixture\Version\UserId',
    ];

    protected $revisions_count = [
        'attribute_code' => 'revisions_count',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $version_number = [
        'attribute_code' => 'version_number',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $created_at = [
        'attribute_code' => 'created_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => 'CURRENT_TIMESTAMP',
        'input' => '',
    ];

    public function getVersionId()
    {
        return $this->getData('version_id');
    }

    public function getLabel()
    {
        return $this->getData('label');
    }

    public function getAccessLevel()
    {
        return $this->getData('access_level');
    }

    public function getPageId()
    {
        return $this->getData('page_id');
    }

    public function getUserId()
    {
        return $this->getData('user_id');
    }

    public function getRevisionsCount()
    {
        return $this->getData('revisions_count');
    }

    public function getVersionNumber()
    {
        return $this->getData('version_number');
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }
}
