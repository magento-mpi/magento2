<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource collection for report rows
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Resource_Report_Settlement_Row_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initializing
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Paypal_Model_Report_Settlement_Row', 'Mage_Paypal_Model_Resource_Report_Settlement_Row');
    }

    /**
     * Join reports info table
     *
     * @return Mage_Paypal_Model_Resource_Report_Settlement_Row_Collection
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
     * @return Mage_Paypal_Model_Resource_Report_Settlement_Row_Collection
     */
    public function addAccountFilter($accountId)
    {
        $this->getSelect()->where('report.account_id = ?', $accountId);
        return $this;
    }
}
