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
 * Report settlement resource model
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Model_Resource_Report_Settlement extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Table name
     *
     * @var string
     */
    protected $_rowsTable;

    /**
     * Init main table
     *
     */
    protected function _construct()
    {
        $this->_init('paypal_settlement_report', 'report_id');
        $this->_rowsTable = $this->getTable('paypal_settlement_report_row');
    }

    /**
     * Save report rows collected in settlement model
     *
     * @param Magento_Paypal_Model_Report_Settlement $object
     * @return Magento_Paypal_Model_Resource_Report_Settlement
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $rows = $object->getRows();
        if (is_array($rows)) {
            $adapter  = $this->_getWriteAdapter();
            $reportId = (int)$object->getId();
            try {
                $adapter->beginTransaction();
                if ($reportId) {
                    $adapter->delete($this->_rowsTable, array('report_id = ?' => $reportId));
                }
                /** @var $date Magento_Core_Model_Date */
                $date = Mage::getSingleton('Magento_Core_Model_Date');

                foreach ($rows as $key => $row) {
                    /*
                     * Converting dates
                     */
                    $completionDate = new Zend_Date($rows[$key]['transaction_completion_date']);
                    $rows[$key]['transaction_completion_date'] = $date->date(null, $completionDate->getTimestamp());
                    $initiationDate = new Zend_Date($rows[$key]['transaction_initiation_date']);
                    $rows[$key]['transaction_initiation_date'] = $date->date(null, $initiationDate->getTimestamp());
                    /*
                     * Converting numeric
                     */
                    $rows[$key]['fee_amount'] = (float)$rows[$key]['fee_amount'];
                    /*
                     * Setting reportId
                     */
                    $rows[$key]['report_id'] = $reportId;
                }
                if (!empty($rows)) {
                    $adapter->insertMultiple($this->_rowsTable, $rows);
                }
                $adapter->commit();
            } catch (Exception $e) {
                $adapter->rollback();
            }
        }

        return $this;
    }

    /**
     * Check if report with same account and report date already fetched
     *
     * @param Magento_Paypal_Model_Report_Settlement $report
     * @param string $accountId
     * @param string $reportDate
     * @return Magento_Paypal_Model_Resource_Report_Settlement
     */
    public function loadByAccountAndDate(Magento_Paypal_Model_Report_Settlement $report, $accountId, $reportDate)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('account_id = :account_id')
            ->where('report_date = :report_date');

        $data = $adapter->fetchRow($select, array(':account_id' => $accountId, ':report_date' => $reportDate));
        if ($data) {
            $report->addData($data);
        }

        return $this;
    }
}
