<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class CustomerGrid
 * Backend customer grid
 *
 * @package Magento\Customer\Test\Block\Adminhtml\Customer
 */
class CustomerGrid extends AbstractGrid
{
    /**
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => '#customerGrid_filter_name',
        ],
        'email' => [
            'selector' => '#customerGrid_filter_email',
        ],
        'group' => [
            'selector' => '#customerGrid_filter_group',
            'input' => 'select',
        ],
    ];
}
