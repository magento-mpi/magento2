<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Widget;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class Chooser
 * Backend Cms Page select product grid
 */
class Chooser extends Grid
{
    protected $filters = [
        'chooser_sku' => [
            'selector' => 'input[name="chooser_sku"]'
        ],
    ];

    /**
     * Locator value for link in sku column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-sku]';
}
