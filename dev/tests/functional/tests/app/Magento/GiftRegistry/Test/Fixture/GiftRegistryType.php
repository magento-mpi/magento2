<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class GiftRegistryType
 * Fixture for gift registry type
 */
class GiftRegistryType extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\GiftRegistry\Test\Repository\GiftRegistryType';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\GiftRegistry\Test\Handler\GiftRegistryType\GiftRegistryTypeInterface';

    protected $defaultDataSet = [
        'code' => 'code_%isolation%',
        'label' => 'gift_registry_label%isolation%',
        'is_listed' => 'Yes',
    ];

    protected $type_id = [
        'attribute_code' => 'type_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '0',
        'input' => '',
    ];

    protected $code = [
        'attribute_code' => 'code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'general'
    ];

    protected $meta_xml = [
        'attribute_code' => 'meta_xml',
        'backend_type' => 'blob',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '0',
        'input' => '',
    ];

    protected $label = [
        'attribute_code' => 'label',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'general'
    ];

    protected $is_listed = [
        'attribute_code' => 'is_listed',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => 'select',
        'group' => 'general'
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $attributes = [
        'attribute_code' => 'attributes',
        'backend_type' => 'virtual',
        'source' => '\Magento\GiftRegistry\Test\Fixture\GiftRegistryType\Attributes',
        'group' => 'attributes'
    ];

    public function getTypeId()
    {
        return $this->getData('type_id');
    }

    public function getCode()
    {
        return $this->getData('code');
    }

    public function getMetaXml()
    {
        return $this->getData('meta_xml');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getLabel()
    {
        return $this->getData('label');
    }

    public function getIsListed()
    {
        return $this->getData('is_listed');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function getAttributes()
    {
        return $this->getData('attributes');
    }
}
