<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Block;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class Grid
 * Backend Cms Block grid
 */
class Grid extends ParentGrid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'identifier' => [
            'selector' => 'input[name="identifier"]'
        ],
        'title' => [
            'selector' => 'input[name="title"]'
        ],
    ];
}
