<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Urlrewrite\Catalog\Category;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * URL Redirect grid
 */
class Grid extends GridInterface
{
    /**
     * Filters array mapping
     *
     * @var array $filters
     */
    protected $filters = [
        'request_path' => [
            'selector' => '#urlrewriteGrid_filter_request_path'
        ]
    ];
}
