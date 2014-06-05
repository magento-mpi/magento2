<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class Grid
 * Attribute Set grid
 */
class Grid extends AbstractGrid
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = '.a-left.col-set_name.last';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'set_name' => [
            'selector' => 'input[name="set_name"]'
        ],
    ];
}
