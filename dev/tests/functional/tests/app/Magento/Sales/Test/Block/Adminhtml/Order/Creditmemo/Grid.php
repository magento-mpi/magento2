<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Sales order grid
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo
 */
class Grid extends GridInterface
{
    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'id' => array(
            'selector' => '#order_creditmemos_filter_increment_id'
        )
    );

    /**
     * Amount refunded
     *
     * @var string
     */
    protected $amountRefunded = 'td.col-refunded.col-base_grand_total';

    /**
     * Refund status
     *
     * @var string
     */
    protected $refundStatus = 'td.col-status.col-state';

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-increment_id';

    /**
     * Get first refund amount
     *
     * @return array|string
     */
    public function getRefundAmount()
    {
        return $this->_rootElement->find($this->amountRefunded)->getText();
    }

    /**
     * Get first status
     *
     * @return array|string
     */
    public function getStatus()
    {
        return $this->_rootElement->find($this->refundStatus)->getText();
    }
}
