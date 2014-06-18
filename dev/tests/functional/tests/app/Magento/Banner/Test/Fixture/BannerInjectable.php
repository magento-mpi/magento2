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
        'input' => '',
    ];

    protected $is_enabled = [
        'attribute_code' => 'is_enabled',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
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
    ];

    protected $store_contents_not_use_0 = [
        'attribute_code' => 'types',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_contents_0 = [
        'attribute_code' => 'types',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_contents_not_use_1 = [
        'attribute_code' => 'types',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_contents_1 = [
        'attribute_code' => 'types',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
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

    public function getStoreContentsNotUse0()
    {
        return $this->getData('store_contents_not_use_0');
    }

    public function getStoreContents0()
    {
        return $this->getData('store_contents_0');
    }

    public function getStoreContentsNotUse1()
    {
        return $this->getData('store_contents_not_use_1');
    }

    public function getStoreContents1()
    {
        return $this->getData('store_contents_1');
    }
}
