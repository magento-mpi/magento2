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
 * Class CatalogProductTemplate
 * Product Template fixture
 */
class CatalogAttributeSet extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Catalog\Test\Repository\CatalogAttributeSet';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Catalog\Test\Handler\CatalogAttributeSet\CatalogAttributeSetInterface';

    protected $defaultDataSet = [
    ];

    protected $attribute_set_id = [
        'attribute_code' => 'attribute_set_id',
        'backend_type' => 'smallint',
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

    protected $attribute_set_name = [
        'attribute_code' => 'attribute_set_name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    public function getAttributeSetId()
    {
        return $this->getData('attribute_set_id');
    }

    public function getEntityTypeId()
    {
        return $this->getData('entity_type_id');
    }

    public function getAttributeSetName()
    {
        return $this->getData('attribute_set_name');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }
}
