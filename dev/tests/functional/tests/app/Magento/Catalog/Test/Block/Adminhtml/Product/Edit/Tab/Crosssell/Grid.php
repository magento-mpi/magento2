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

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

class Grid extends GridInterface
{
    /**
     * Grid fields map
     *
     * @var array
     */
    protected $filters = array(
        'name' => array(
            'selector' => '#cross_sell_product_grid_filter_name'
        ),
        'sku' => array(
            'selector' => '#cross_sell_product_grid_filter_sku'
        ),
        'type' => array(
            'selector' => '#cross_sell_product_grid_filter_type',
            'input' => 'select'
        )
    );
}
