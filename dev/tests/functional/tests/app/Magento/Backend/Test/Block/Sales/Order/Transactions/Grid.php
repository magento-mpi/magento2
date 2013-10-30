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

namespace Magento\Backend\Test\Block\Sales\Order\Transactions;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Sales order grid
 *
 * @package Magento\Backend\Test\Block\Sales\Order\Transactions
 */
class Grid extends GridInterface
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'id' => array(
                'selector' => '#order_transactions_filter_txn_id'
            )
        );
    }

    /**
     * Get Transaction type
     *
     * @return array|string
     */
    public function getTransactionType()
    {
        return $this->_rootElement->find('td.col-transaction-type.col-txn_type')->getText();
    }
}
