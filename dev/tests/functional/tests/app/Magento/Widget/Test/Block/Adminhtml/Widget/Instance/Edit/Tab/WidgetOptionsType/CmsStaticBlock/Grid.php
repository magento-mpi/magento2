<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CmsStaticBlock;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Chooser block grid
 */
class Grid extends GridInterface
{
    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr td.a-left.col-chooser_title';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => 'input[name="chooser_title"]'
        ],
        'identifier' => [
            'selector' => 'input[name="chooser_identifier"]'
        ],
    ];
}
