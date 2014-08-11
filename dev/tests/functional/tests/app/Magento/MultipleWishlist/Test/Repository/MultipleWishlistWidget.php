<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class MultipleWishlistWidget Repository
 * Repository for multiple wish list widget
 */
class MultipleWishlistWidget extends AbstractRepository
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
        $this->_data['add_search'] = [
            'title' => 'Wishlist search %isolation%',
            'store_ids' => [
                '0' => '0'
            ],
            'widget_instance' => [
                '0' => [
                    'page_group' => 'all_pages',
                    'all_pages' => [
                        'page_id' => '0',
                        'layout_handle' => 'catalog_product_view',
                        'for' => 'all',
                        'block' => 'sidebar.main',
                        'template' => 'widget/search.phtml'
                    ]
                ]
            ],
            'parameters' => [
                'types' => ['email']
            ],
            'theme_id' => '2',
            'code' => 'wishlist_search',
            'sort_order' => 0
        ];
    }
}
