<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Block\Adminhtml\Promo\Quote\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class BannersGrid
 * Banners grid on Cart Price Rules page
 */
class BannersGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'banner_name' => [
            'selector' => 'input[name="banner_name"]',
        ],
    ];
}
