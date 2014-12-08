<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Widget Repository.
 */
class Widget extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['order_by_sku'] = [
            'code' => 'Order by SKU',
            'theme_id' => 'Magento Blank',
            'title' => 'Order by SKU %isolation%',
            'store_ids' => ['dataSet' => 'All Store Views'],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'All Pages',
                    'all_pages' => [
                        'block' => 'Sidebar Additional',
                    ],
                ],
            ],
            'parameters' => [
                'display_mode' => 'fixed',
                'anchor_text' => 'text',
                'title' => 'anchor title',
            ],
            'page_id' => ['dataSet' => 'default'],
        ];
    }
}
