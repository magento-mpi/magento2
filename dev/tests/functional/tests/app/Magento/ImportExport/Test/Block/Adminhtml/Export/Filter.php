<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ImportExport\Test\Block\Adminhtml\Export;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;
use Mtf\Client\Element;

/**
 * Class Filter
 * Filter for export
 */
class Filter extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'frontend_label' => [
            'selector' => 'input[name="frontend_label"]'
        ],
        'attribute_code' => [
            'selector' => 'input[name="attribute_code"]'
        ],
    ];
}
