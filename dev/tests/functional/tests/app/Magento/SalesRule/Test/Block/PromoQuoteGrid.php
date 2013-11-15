<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Block;

use Magento\Backend\Test\Block\Widget\Grid;

class PromoQuoteGrid extends Grid
{
    protected function _init()
    {
        parent::_init();
        $this->filters = [
            'name' => [
                'selector' => '#promo_quote_grid_filter_name'
            ]
        ];
    }
}
