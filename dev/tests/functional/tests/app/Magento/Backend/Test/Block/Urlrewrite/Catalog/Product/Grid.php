<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Urlrewrite\Catalog\Product;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Product grid
 *
 */
class Grid extends GridInterface
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = array(
        'id' => array(
            'selector' => '[id=productGrid_product_filter_entity_id]',
        ),
        'sku' => array(
            'selector' => '[id=productGrid_product_filter_sku]',
        ),
    );

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->selectItem = 'tbody tr .col-entity_id';
    }
}
