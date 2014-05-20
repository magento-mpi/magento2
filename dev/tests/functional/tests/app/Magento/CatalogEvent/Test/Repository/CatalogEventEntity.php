<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * @constructor
 * @param array $defaultConfig
 * @param array $defaultData
 */
class CatalogEventEntity extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default_event'] = [
            'date_start' => ['pattern' => 'm-d-Y 12:00 a-3 days'],
            'date_end' => ['pattern' => 'm-d-Y 12:00 a+3 days'],
            'sort_order' => '1',
            'display_state' => [
                'category_page' => 'Yes',
                'product_page' => 'Yes'
            ],
            'category_ids' => ['presets' => 'default_subcategory']
        ];
    }
}
