<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class NewsletterTemplateGrid
 *
 * @package Magento\Newsletter\Test\Block\Aminhtml
 */
class NewsletterTemplateGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array $filters
     */
    protected $filters = [
        'code' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-text-1-filter-code"]'
        ]
    ];
}
