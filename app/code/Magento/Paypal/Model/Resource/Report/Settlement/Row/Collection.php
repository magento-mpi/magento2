<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Resource collection for report rows
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Model\Resource\Report\Settlement\Row;

class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initializing
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\Paypal\Model\Report\Settlement\Row',
            'Magento\Paypal\Model\Resource\Report\Settlement\Row'
        );
    }

    /**
     * Join reports info table
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(
            ['report' => $this->getTable('paypal_settlement_report')],
            'report.report_id = main_table.report_id',
            ['report.account_id', 'report.report_date']
        );
        return $this;
    }

    /**
     * Filter items collection by account ID
     *
     * @param string $accountId
     * @return $this
     */
    public function addAccountFilter($accountId)
    {
        $this->getSelect()->where('report.account_id = ?', $accountId);
        return $this;
    }
}
