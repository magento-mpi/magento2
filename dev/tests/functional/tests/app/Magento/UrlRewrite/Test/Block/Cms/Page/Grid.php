<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Block\Cms\Page;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * URL Redirect grid
 */
class Grid extends GridInterface
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td.col-title';

    /**
     * Filters array mapping
     *
     * @var array $filters
     */
    protected $filters = [
        'title' => [
            'selector' => '#cmsPageGrid_filter_title'
        ]
    ];
}
