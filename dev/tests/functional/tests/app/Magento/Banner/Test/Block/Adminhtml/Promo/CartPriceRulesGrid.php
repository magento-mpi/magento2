<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Block\Adminhtml\Promo;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class CartPriceRulesGrid
 * Cart Price Rules Grid block on Banner new page
 */
class CartPriceRulesGrid extends Grid
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => 'input[name="salesrule_name"]'
        ],
        'id' => [
            'selector' => 'input[name="salesrule_rule_id"]'
        ]
    ];
}
