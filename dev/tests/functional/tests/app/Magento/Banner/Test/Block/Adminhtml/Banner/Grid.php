<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Block\Adminhtml\Banner;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class Grid
 * Gift card account grid block
 */
class Grid extends AbstractGrid
{
    /**
     * Path for types
     *
     * @var string
     */
    protected $typesPath = '//td[contains(@class,"col-banner_types") and contains(.,"%s")]';

    /**
     * Locator value for banner name
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-banner_name]';

    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'banner' => [
            'selector' => 'input[name="banner_name"]',
        ],
        'visibility' => [
            'selector' => 'select[name="visible_in"]',
            'input' => 'selectstore',
        ],
        'active' => [
            'selector' => 'select[name="banner_is_enabled"]',
            'input' => 'select',
        ],
    ];
}
