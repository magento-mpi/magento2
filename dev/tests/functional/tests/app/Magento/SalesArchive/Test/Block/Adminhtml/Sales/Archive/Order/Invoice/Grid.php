<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\Block\Adminhtml\Sales\Archive\Order\Invoice;

/**
 * Class Grid
 * Sales archive invoices grid
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'invoice_id' => [
            'selector' => 'input[name="increment_id"]',
        ],
        'order_id' => [
            'selector' => 'input[name="order_increment_id"]',
        ],
    ];
}
