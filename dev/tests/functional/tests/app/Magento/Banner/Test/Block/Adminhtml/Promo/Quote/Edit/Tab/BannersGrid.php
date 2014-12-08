<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
