<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Block\Adminhtml\Sales\Archive\Order\Creditmemo;

/**
 * Class Grid
 * Sales archive credit memos grid
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'creditmemo_id' => [
            'selector' => 'input[name="real_creditmemo_id"]',
        ],
        'order_id' => [
            'selector' => 'input[name="order_increment_id"]',
        ],
    ];
}
