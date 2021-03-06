<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Block\Adminhtml\Promo;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class CatalogPriceRulesGrid
 * Cart Catalog Price Rules Grid block on Banner new page
 */
class CatalogPriceRulesGrid extends Grid
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => 'input[name="catalogrule_name"]',
        ],
        'id' => [
            'selector' => 'input[name="catalogrule_rule_id"]',
        ],
    ];
}
