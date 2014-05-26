<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rating\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;

class RatingGrid extends Grid
{
    /**
     * Locator value for rating code column
     *
     * @var string
     */
    protected $editLink = 'td[data-column="rating_code"]';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'rating_code' => [
            'selector' => '[name="rating_code"]',
        ],
        'is_active' => [
            'selector' => '[name="is_active"]',
            'input' => 'select',
        ],
    ];
}
