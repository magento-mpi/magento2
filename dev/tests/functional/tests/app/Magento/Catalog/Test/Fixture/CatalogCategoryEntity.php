<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CatalogCategoryEntity
 *
 * @package Magento\Catalog\Test\Fixture
 */
class CatalogCategoryEntity extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Catalog\Test\Repository\CatalogCategoryEntity';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Catalog\Test\Handler\CatalogCategoryEntity\CatalogCategoryEntityInterface';

    protected $defaultDataSet = [
        'name' => 'Category%isolation%',
        'path' => '2',
        'url_key' => 'category%isolation%',
        'is_active' => '1',
        'include_in_menu' => '1',
    ];

    protected $entity_id = [
        'attribute_code' => 'entity_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $entity_type_id = [
        'attribute_code' => 'entity_type_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $attribute_set_id = [
        'attribute_code' => 'attribute_set_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $parent_id = [
        'attribute_code' => 'parent_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $created_at = [
        'attribute_code' => 'created_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $updated_at = [
        'attribute_code' => 'updated_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $path = [
        'attribute_code' => 'path',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $position = [
        'attribute_code' => 'position',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $level = [
        'attribute_code' => 'level',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $children_count = [
        'attribute_code' => 'children_count',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $id = [
        'attribute_code' => 'id',
        'backend_type' => 'virtual',
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'virtual',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'virtual',
    ];

    protected $url_key = [
        'attribute_code' => 'url_key',
        'backend_type' => 'virtual',
    ];

    protected $include_in_menu = [
        'attribute_code' => 'include_in_menu',
        'backend_type' => 'virtual',
    ];

    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    public function getEntityTypeId()
    {
        return $this->getData('entity_type_id');
    }

    public function getAttributeSetId()
    {
        return $this->getData('attribute_set_id');
    }

    public function getParentId()
    {
        return $this->getData('parent_id');
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    public function getPath()
    {
        return $this->getData('path');
    }

    public function getPosition()
    {
        return $this->getData('position');
    }

    public function getLevel()
    {
        return $this->getData('level');
    }

    public function getChildrenCount()
    {
        return $this->getData('children_count');
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getUrlKey()
    {
        return $this->getData('url_key');
    }

    public function getIncludeInMenu()
    {
        return $this->getData('include_in_menu');
    }

    /**
     * Get product name
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getData('fields/name/value');
    }
}
