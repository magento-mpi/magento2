<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Block;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class CmsGrid
 * Adminhtml Cms Block management grid
 */
class CmsGrid extends GridInterface
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => 'input[name="title"]'
        ],
        'identifier' => [
            'selector' => 'input[name="identifier"]',
        ],
        'store_id' => [
            'selector' => 'select[name="store_id"]',
            'input' => 'select',
        ],
        'is_active' => [
            'selector' => 'select[name="is_active"]',
            'input' => 'select',
        ],
        'creation_time_from' => [
            'selector' => 'input[name="creation_time[from]"]',
        ],
        'update_time_from' => [
            'selector' => 'input[name="update_time[from]"]',
        ],
    ];
}
