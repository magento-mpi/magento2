<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class CustomerCustomAttributesGrid
 * Adminhtml CustomerCustomAttributes block management grid
 */
class CustomerCustomAttributesGrid extends GridInterface
{
    /**
     * An element locator which allows to select first entity in grid
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-frontend_label]';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'attribute_code' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-text-filter-attribute-code"]'
        ],
        'frontend_label' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-text-1-filter-frontend-label"]',
        ],
        'is_required' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-select-extended-filter-is-required"]',
            'input' => 'select',
        ],
        'is_user_defined' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-select-extended-1-filter-is-user-defined"]',
            'input' => 'select',
        ],
        'is_visible' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-select-extended-2-filter-is-visible"]',
            'input' => 'select',
        ],
        'sort_order' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-text-2-filter-sort-order"]',
        ],
    ];
}
