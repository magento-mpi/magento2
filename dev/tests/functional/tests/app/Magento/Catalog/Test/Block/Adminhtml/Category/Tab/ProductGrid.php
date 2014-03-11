<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Category\Tab;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class ProductGrid
 * Products' grid of Category Products tab
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Category\Tab
 */
class ProductGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'sku' => [
            'selector' => '#catalog_category_products_filter_sku'
        ]
    ];

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-in_category';
}