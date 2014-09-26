<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Block;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class CmsGrid
 * Adminhtml Cms Block management grid
 */
class CmsGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => '#title'
        ],
        'identifier' => [
            'selector' => '#identifier',
        ],
        'is_active' => [
            'selector' => '#is_active',
            'input' => 'select',
        ],
        'creation_time_from' => [
            'selector' => '(//span[.="Created"]/following::input[contains(@placeholder,"From")])[1]',
            'strategy' => 'xpath',
        ],
        'update_time_from' => [
            'selector' => '(//span[.="Created"]/following::input[contains(@placeholder,"From")])[2]',
            'strategy' => 'xpath',
        ],
    ];

    /**
     * An element locator which allows to select first entity in grid
     *
     * @var string
     */
    protected $editLink = 'td[data-column="title"]';
}
