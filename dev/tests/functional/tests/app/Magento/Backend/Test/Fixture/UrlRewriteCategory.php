<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class UrlRewriteCategory
 */
class UrlRewriteCategory extends InjectableFixture
{
    protected $defaultDataSet = [
        'store_id' => null,
        'request_path' => null,
    ];

    protected $id = [
        'attribute_code' => 'id',
        'backend_type' => 'virtual',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => 'Default Store View',
        'input' => 'select',
    ];

    protected $options = [
        'attribute_code' => 'options',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'input' => 'select',
    ];

    protected $request_path = [
        'attribute_code' => 'request_path',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => 'request_path%isolation%',
        'input' => 'text',
    ];

    protected $description = [
        'attribute_code' => 'description',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'input' => 'text',
    ];

    public function getId()
    {
        return $this->getData('id');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getOptions()
    {
        return $this->getData('options');
    }

    public function getRequestPath()
    {
        return $this->getData('request_path');
    }

    public function getDescription()
    {
        return $this->getData('description');
    }
}
