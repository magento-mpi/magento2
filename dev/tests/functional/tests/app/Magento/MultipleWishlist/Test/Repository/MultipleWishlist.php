<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class MultipleWishlist Repository
 * Repository for multiple wish list
 */
class MultipleWishlist extends AbstractRepository
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
        $this->_data['wishlist_private'] = [
            'customer_id' => ['dataSet' => 'default'],
            'name' => 'Wish list %isolation%',
            'visibility' => 'No',
        ];

        $this->_data['wishlist_public'] = [
            'customer_id' => ['dataSet' => 'default'],
            'name' => 'Wish list %isolation%',
            'visibility' => 'Yes',
        ];

        $this->_data['wishlist_private_without_customer'] = [
            'name' => 'Wish list %isolation%',
            'visibility' => 'No',
        ];

        $this->_data['wishlist_public_without_customer'] = [
            'name' => 'Wish list %isolation%',
            'visibility' => 'Yes',
        ];
    }
}
