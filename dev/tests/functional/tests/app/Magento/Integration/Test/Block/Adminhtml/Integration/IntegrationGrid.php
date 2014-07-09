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
 * Integrations grid block
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
            'selector' => 'input[name="name"]',
        ],
        'status' => [
            'selector' => 'input[name="status"]',
            'input' => 'select',
        ]
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = '[data-column="edit"] button';
}
