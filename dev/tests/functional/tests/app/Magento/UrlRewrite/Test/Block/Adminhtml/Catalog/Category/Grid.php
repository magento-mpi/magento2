<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Category;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class Grid
 * URL Rewrite grid
 */
class Grid extends ParentGrid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'request_path' => [
            'selector' => '#urlrewriteGrid_filter_request_path'
        ],
        'id_path' => [
            'selector' => '#urlrewriteGrid_filter_id_path'
        ],
        'target_path' => [
            'selector' => 'input[name="target_path"]'
        ]
    ];
}
