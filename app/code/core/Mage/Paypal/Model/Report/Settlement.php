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
 * Paypal Settlement Report model
 *
 * Perform fetching reports from remote servers with following saving them to database
 * Prepare report rows for Mage_Paypal_Model_Report_Settlement_Row model
 *
 */
class Mage_Paypal_Model_Report_Settlement extends Mage_Core_Model_Abstract
{
    /**
     * Default PayPal SFTP host
     * @var string
     */
    const REPORTS_HOSTNAME = "reports.paypal.com";

    /**
     * Default PayPal SFTP host for sandbox mode
     * @var string
     */
    const SANDBOX_REPORTS_HOSTNAME = "reports.sandbox.paypal.com";

    /**
     * PayPal SFTP path
     * @var string
     */
    const REPORTS_PATH = "/ppreports/outgoing";

    /**
     * Reports rows storage
     * @var array
     */
    protected $_rows = array();

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('paypal/report_settlement');
    }

    /**
     * Goes to specified host/path and fetches reports from there.
     * Save reports to database.
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $path
     * @return Mage_Paypal_Model_Report_Settlement
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
                $this->setReportDate($this->_fileNameToDate($filename))
                    ->setFilename($filename)
                    ->parseCsv($localCsv)
                    ->save();

                $io = new Varien_Io_File();
                $io->rm($localCsv);
            }
        }
        return $this;
    }

    /**
     * Fetch and save reports for all existing SFTP settings
     *
     * @param bool $automaticMode Whether to skip settings with disabled Automatic Fetching or not
     * @return Mage_Paypal_Model_Report_Settlement
     */
    public function fetchAllReports($automaticMode = false)
    {
        $configs = $this->_getSftpConfigs($automaticMode);
        foreach ($configs as $config) {
            $this->fetchAndSave($config['hostname'], $config['username'], $config['password'], $config['path']);
        }
        return $this;
    }

    /**
     * Parse CSV file and collect report rows
     *
     * @param string $localCsv Path to CSV file
     * @return Mage_Paypal_Model_Report_Settlement
     */
    public function parseCsv($localCsv)
    {
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
                    $this->loadByAccountAndDate();
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
     * Load report by unique key (accoutn + report date)
     *
     * @return Mage_Paypal_Model_Report_Settlement
     */
    public function loadByAccountAndDate()
    {
        $this->getResource()->loadByAccountAndDate($this, $this->getAccountId(), $this->getReportDate());
        return $this;
    }

    /**
     * Return collected rows for further processing.
     *
     * @return array
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Return name for row column
     *
     * @param string $field Field name in row model
     * @return string
     */
    public function getFieldLabel($field)
    {
        $labels = array(
            'report_date'                   => 'Report Date',
            'account_id'                    => 'Merchant Account',
            'transaction_id'                => 'Transaction ID',
            'invoice_id'                    => 'Invoice ID',
            'paypal_reference_id'           => 'PayPal Reference ID',
            'paypal_reference_id_type'      => 'PayPal Reference ID Type',
            'transaction_event_code'        => 'Event Code',
            'transaction_event'             => 'Event',
            'transaction_initiation_date'   => 'Initiation Date',
            'transaction_completion_date'   => 'Completion Date',
            'transaction_debit_or_credit'   => 'Debit or Credit',
            'gross_transaction_amount'      => 'Gross Amount',
            'fee_debit_or_credit'           => 'Fee Debit or Credit',
            'fee_amount'                    => 'Fee Amount',
            'custom_field'                  => 'Custom',
        );
        if (isset($labels[$field])) {
            return Mage::helper('paypal')->__($labels[$field]);
        }
        return '';
    }

    /**
     * Iterate through website configurations and collect all SFTP configurations
     * Filter config values if necessary
     *
     * @param bool $automaticMode Whether to skip settings with disabled Automatic Fetching or not
     * @return array
     */
    protected function _getSftpConfigs($automaticMode = false)
    {
        $configs = array();
        $uniques = array();
        foreach(Mage::app()->getStores() as $store) {
            /*@var $store Mage_Core_Model_Store */
            $active = (bool)$store->getConfig('paypal/fetch_reports/active');
            if (!$active && $automaticMode) {
                continue;
            }
            $cfg = array(
                'hostname'  => $store->getConfig('paypal/fetch_reports/ftp_ip'),
                'path'      => $store->getConfig('paypal/fetch_reports/ftp_path'),
                'username'  => $store->getConfig('paypal/fetch_reports/ftp_login'),
                'password'  => $store->getConfig('paypal/fetch_reports/ftp_password'),
                'sandbox'   => $store->getConfig('paypal/fetch_reports/ftp_sandbox'),
            );
            if (empty($cfg['hostname']) || $cfg['sandbox']) {
                $cfg['hostname'] = $cfg['sandbox'] ? self::SANDBOX_REPORTS_HOSTNAME : self::REPORTS_HOSTNAME;
            }
            if (empty($config['path']) || $cfg['sandbox']) {
                $cfg['path'] = self::REPORTS_PATH;
            }
            // avoid duplicates
            if (in_array(serialize($cfg), $uniques)) {
                continue;
            }
            $uniques[] = serialize($cfg);
            $configs[] = $cfg;
        }
        return $configs;
    }

    /**
     * Converts a filename to date of report.
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
     * Filter SFTP file list by filename format
     *
     * @param array $list List of files as per $connection->rawls()
     * @return array Trimmed down list of files
     */
    protected function _filterReportsList($list)
    {
        $result = array();
        $pattern = '/^STL-(\d{8,8})\.(\d{2,2})\.(.{3,3})\.CSV$/';
        foreach ($list as $filename => $data) {
            if (preg_match($pattern, $filename)) {
                $result[$filename] = $data;
            }
        }
        return $result;
    }
}
