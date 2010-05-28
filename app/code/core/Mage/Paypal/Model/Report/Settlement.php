<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/*
 *
 * Paypal Settlement Report — writing side.
 *
 * This class allows interface reports on a "per-file" basis, that is, as they
 * are being read from PayPal. The class contains necessary methods to even retrieve
 * all its rows but it is *not* a recommended way to read-interface the reports. For
 * that, please use Mage_Paypal_Model_Report_Settlement_Collection.
 *
 */
class Mage_Paypal_Model_Report_Settlement extends Mage_Core_Model_Abstract
{
    const REPORTS_HOSTNAME = "reports.paypal.com";
    const SANDBOX_REPORTS_HOSTNAME = "reports.sandbox.paypal.com";
    const REPORTS_PATH = "/ppreports/outgoing";

    protected $_rows = null;
    protected $_maySkipExistingReports = true;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('paypal/report_settlement');
    }

    /**
     * Goes to specified host/path and fetches reports from there.
     */
    public function fetchAndSave($host, $username, $password, $path)
    {
        $connection = new Varien_Io_Sftp();
        $connection->open(array('host' => $host, 'username' => $username, 'password' => $password));
        $connection->cd($path);
        $listing = $this->_filterReportsList($connection->rawls());
        foreach ($listing as $filename => $attributes) {
            $localCsv = Mage::getConfig()->getOptions()->getTmpDir() . DS . $filename;
            if ($connection->read($filename, $localCsv)) {
                $this->saveFromCsv($localCsv)
                    ->setFilename($filename)
                    ->setReportDate($this->_fileNameToDate($filename))
                    ->setMaySkipExistingReport(true)
                    ->save();

                $io = new Varien_Io_File();
                $io->rm($localCsv);
            }
        }
    }

    /**
     * Parse CSV file and save report data
     *
     * @param string $localCsv Path to CSV file
     * @return Mage_Paypal_Model_Report_Settlement
     */
    public function saveFromCsv($localCsv)
    {
        // If a filename is given but contents is not, read in the file
        // as if it were local.
        $this->_rows = array();

        $section_columns = array('' => 0,
            'TransactionID' => 1,
            'InvoiceID' => 2,
            'PayPalReferenceID' => 3,
            'PayPalReferenceIDType' => 4,
            'TransactionEventCode' => 5,
            'TransactionInitiationDate' => 6,
            'TransactionCompletionDate' => 7,
            'TransactionDebitOrCredit' => 8,
            'GrossTransactionAmount' => 9,
            'GrossTransactionCurrency' => 10,
            'FeeDebitOrCredit' => 11,
            'FeeAmount' => 12,
            'FeeCurrency' => 13,
            'CustomField' => 14,
            'ConsumerID' => 15,
            );
        $rowmap = array(
            'TransactionID' => 'transaction_id',
            'InvoiceID' => 'invoice_id',
            'PayPalReferenceID' => 'paypal_reference_id',
            'PayPalReferenceIDType' => 'paypal_reference_id_type',
            'TransactionEventCode' => 'transaction_event_code',
            'TransactionInitiationDate' => 'transaction_initiation_date',
            'TransactionCompletionDate' => 'transaction_completion_date',
            'TransactionDebitOrCredit' => 'transaction_debit_or_credit',
            'GrossTransactionAmount' => 'gross_transaction_amount',
            'GrossTransactionCurrency' => 'gross_transaction_currency',
            'FeeDebitOrCredit' => 'fee_debit_or_credit',
            'FeeAmount' => 'fee_amount',
            'FeeCurrency' => 'fee_currency',
            'CustomField' => 'custom_field',
            'ConsumerID' => 'consumer_id',
        );
        $flipped_section_columns = array_flip($section_columns);
        $fp = fopen($localCsv, 'r');
        while($line = fgetcsv($fp)) {
            if (empty($line)) { // The line was empty, so skip it.
                continue;
            }
            $lineType = $line[0];
            switch($lineType) {
                case 'RH': // Report header.
                    $this->setLastModified($line[1]);
                    //$this->setAccountId($columns[2]); -- probably we'll just take that from the section header...
                    break;
                case 'FH': // File header.
                    // Nothing interesting here, move along
                    break;
                case 'SH': // Section header.
                    $this->setAccountId($line[3]);
                    $this->loadByAccountAndDate($this->getAccountId(), $this->getReportDate());
                    break;
                case 'CH': // Section columns.
                    // In case ever the column order is changed, we will have the items recorded properly
                    // anyway. We have named, not numbered columns.
                    for ($i = 1; $i < count($line); $i++) {
                        $section_columns[$line[$i]] = $i;
                    }
                    $flipped_section_columns = array_flip($section_columns);
                    break;
                case 'SB': // Section body.
                    $bodyitem = array();
                    for($i = 1; $i < count($line); $i++) {
                        $bodyitem[$rowmap[$flipped_section_columns[$i]]] = $line[$i];
                    }
                    $this->_rows[] = $bodyitem;
                    break;
                case 'SC': // Section records count.
                case 'RC': // Report records count.
                case 'SF': // Section footer.
                case 'FF': // File footer.
                case 'RF': // Report footer.
                    // Nothing to see here, move along
                    break;
            }
        }
        return $this;
    }

    /**
     *
     * Return collected rows for further processing.
     *
     * @return array
     */
    public function getRows()
    {
        return $this->_rows;
    }

    public function setMaySkipExistingReports($value)
    {
        $this->_maySkipExistingReports = (bool) $value;
        return $this;
    }

    public function getMaySkipExistingReports()
    {
        return $this->_maySkipExistingReports;
    }

    public function loadByAccountAndDate($accountID, $reportDate)
    {
        return $this->addData($this->getResource()->loadByAccountAndDate($accountID, $reportDate));
    }

    /**
     * Converts a filename to date of report. This method should be
     * modified if naming scheme changes.
     *
     * @param string $filename
     * @return string
     */
    protected function _fileNameToDate($filename)
    {
        // Currently filenames look like STL-YYYYMMDD, so that is what we care about.
        $dateSnippet = substr(basename($filename), 4, 8);
        $result = substr($dateSnippet, 0, 4).'-'.substr($dateSnippet, 4, 2).'-'.substr($dateSnippet, 6, 2);
        return $result;
    }

    /**
     *
     * Takes in a list of files as reported by connection->rawls(), and returns
     * only those items which look like settlement reports.
     *
     * @param array $list List of files as per $connection->rawls()
     * @return array Trimmed down list of files
     *
     */
    protected function _filterReportsList($list)
    {
        $result = array();
        $pattern = '/^STL-(\d{8,8})\.(\d{2,2})\.(.{3,3})\.CSV$/';
//        $pattern = '/\.csv$/';
        foreach ($list as $filename => $data) {
            if (preg_match($pattern, $filename)) {
                $result[$filename] = $data;
            }
        }
        return $result;
    }
}
