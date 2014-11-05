<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CatalogEventEntity
 */
class CatalogEventEntity extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\CatalogEvent\Test\Repository\CatalogEventEntity';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\CatalogEvent\Test\Handler\CatalogEventEntity\CatalogEventEntityInterface';

    protected $defaultDataSet = [
        'date_start' => ['pattern' => 'm/d/Y 12:00 a-3 days'],
        'date_end' => ['pattern' => 'm/d/Y 12:00 a+2 days'],
        'sort_order' => '1',
        'display_state' => [
            'category_page' => 'Yes',
            'product_page' => 'Yes'
        ],
        'category_id' => ['presets' => 'default_subcategory'],
    ];

    protected $event_id = [
        'attribute_code' => 'event_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $category_id = [
        'attribute_code' => 'category_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\CatalogEvent\Test\Fixture\CatalogEventEntity\CategoryId',
    ];

    protected $date_start = [
        'attribute_code' => 'date_start',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Backend\Test\Fixture\Date',
    ];

    protected $date_end = [
        'attribute_code' => 'date_end',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Backend\Test\Fixture\Date',
    ];

    protected $display_state = [
        'attribute_code' => 'display_state',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => 'checkbox',
    ];

    protected $sort_order = [
        'attribute_code' => 'sort_order',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    public function getEventId()
    {
        return $this->getData('event_id');
    }

    public function getCategoryId()
    {
        return $this->getData('category_id');
    }

    public function getDateStart()
    {
        return $this->getData('date_start');
    }

    public function getDateEnd()
    {
        return $this->getData('date_end');
    }

    public function getDisplayState()
    {
        return $this->data['display_state'];
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }
}
