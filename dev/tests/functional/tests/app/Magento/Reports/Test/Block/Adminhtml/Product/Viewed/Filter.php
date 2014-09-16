<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Product\Viewed;

use Magento\Reports\Test\Block\Adminhtml\AbstractFilter;

/**
 * Class Filter
 * Filter for Product Views Report
 */
class Filter extends AbstractFilter
{
    /**
     * Specified fields
     *
     * @var array
     */
    protected $names = ['period_type', 'show_empty_rows'];
}
