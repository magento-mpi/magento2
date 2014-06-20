<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class BannerInjectable
 * Banner fixture
 */
class BannerInjectable extends InjectableFixture
{
    protected $defaultDataSet = [
        'name' => 'banner_%isolation%',
        'is_enabled' => 'Yes',
    ];

    protected $banner_id = [
        'attribute_code' => 'banner_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'group' => 'banner_properties',
        'input' => '',
    ];

    protected $is_enabled = [
        'attribute_code' => 'is_enabled',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'group' => 'banner_properties',
        'input' => '',
    ];

    protected $types = [
        'attribute_code' => 'types',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $use_customer_segment = [
        'attribute_code' => 'types',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
        'group' => 'banner_properties',
    ];

    protected $customer_segment = [
        'attribute_code' => 'types',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => 'multiselect',
        'group' => 'banner_properties',
        'source' => 'Magento\Banner\Test\Fixture\BannerInjectable\CustomerSegment',
    ];

    protected $store_contents_not_use = [
        'attribute_code' => 'types',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'content',
    ];

    protected $store_contents = [
        'attribute_code' => 'types',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'content',
    ];

    public function getBannerId()
    {
        return $this->getData('banner_id');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getIsEnabled()
    {
        return $this->getData('is_enabled');
    }

    public function getTypes()
    {
        return $this->getData('types');
    }

    public function getUseCustomerSegment()
    {
        return $this->getData('use_customer_segment');
    }

    public function getStoreContentsNotUse()
    {
        return $this->getData('store_contents_not_use');
    }

    public function getStoreContents()
    {
        return $this->getData('store_contents');
    }
}
