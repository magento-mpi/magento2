<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class WidgetGrid
 * Widget grid on the Widget Instance Index page
 */
class WidgetGrid extends AbstractGrid
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'tbody tr td.col-title';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => 'input[name="title"]'
        ]
    ];
}
