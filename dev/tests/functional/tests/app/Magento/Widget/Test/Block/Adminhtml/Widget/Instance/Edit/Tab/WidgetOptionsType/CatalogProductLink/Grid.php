<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CatalogProductLink;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Chooser product grid
 */
class Grid extends GridInterface
{
    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr td.col-sku.col-chooser_sku';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => 'input[name="chooser_name"]',
        ],
        'sku' => [
            'selector' => 'input[name="chooser_sku"]',
        ],
    ];
}
