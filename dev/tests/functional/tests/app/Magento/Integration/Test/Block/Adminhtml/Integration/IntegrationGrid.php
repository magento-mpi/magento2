<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml\Integration;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class IntegrationGrid
 * Integrations grid block.
 */
class IntegrationGrid extends Grid
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => '#integrationGrid_filter_name',
        ],
        'status' => [
            'selector' => '#integrationGrid_filter_status',
            'input' => 'select',
        ]
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-edit] > #edit';
}
