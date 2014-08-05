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
        'user_id' => 'admin'
    ];

    protected $label = [
        'attribute_code' => 'label',
        'backend_type' => 'varchar',
        'is_required' => '1',
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

    protected $user_id = [
        'attribute_code' => 'user_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
        'source' => 'Magento\VersionsCms\Test\Fixture\Version\UserId',
    ];

    public function getLabel()
    {
        return $this->getData('label');
    }

    public function getAccessLevel()
    {
        return $this->getData('access_level');
    }

    public function getUserId()
    {
        return $this->getData('user_id');
    }
}
