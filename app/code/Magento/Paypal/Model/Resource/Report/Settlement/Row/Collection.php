<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource collection for report rows
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Model\Resource\Report\Settlement\Row;

class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initializing
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Paypal\Model\Report\Settlement\Row', 'Magento\Paypal\Model\Resource\Report\Settlement\Row');
    }

    /**
     * Join reports info table
     *
     * @return \Magento\Paypal\Model\Resource\Report\Settlement\Row\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(
                array('report' => $this->getTable('paypal_settlement_report')),
                'report.report_id = main_table.report_id',
                array('report.account_id', 'report.report_date')
            );
        return $this;
    }

    /**
     * Filter items collection by account ID
     *
     * @param string $accountId
     * @return \Magento\Paypal\Model\Resource\Report\Settlement\Row\Collection
     */
    public function addAccountFilter($accountId)
    {
        $this->getSelect()->where('report.account_id = ?', $accountId);
        return $this;
    }
}
