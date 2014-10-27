<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Test\Block\Adminhtml\Rma;

/**
 * Grid on backend rma index page.
 */
class RmaGrid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'order_id_from' => [
            'selector' => 'input[name="order_increment_id[from]"]'
        ],
        'order_id_to' => [
            'selector' => 'input[name="order_increment_id[to]"]'
        ],
        'customer' => [
            'selector' => 'input[name="customer_name"]'
        ],
        'status' => [
            'selector' => 'select[name="status"]',
            'input' => 'select'
        ]
    ];
}
