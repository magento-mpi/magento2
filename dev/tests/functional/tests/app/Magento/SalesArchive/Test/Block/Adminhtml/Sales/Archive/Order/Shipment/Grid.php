<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\Block\Adminhtml\Sales\Archive\Order\Shipment;

/**
 * Class Grid
 * Sales archive shipments grid
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'shipment_id' => [
            'selector' => 'input[name="real_shipment_id"]',
        ],
        'order_id' => [
            'selector' => 'input[name="order_increment_id"]',
        ],
    ];
}
