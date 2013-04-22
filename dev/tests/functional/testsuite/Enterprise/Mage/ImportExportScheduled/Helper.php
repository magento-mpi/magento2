<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scheduled Import Export Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_ImportExportScheduled_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * FTP Connection handle
     *
     * @var resource
     */
    protected static $_connId = NULL;
    /**
     * FTP Login result status
     *
     * @var null
     */
    protected static $_loginResult = NULL;

    /**
     * Establish connection to FTP server
     *
     * @param string $host
     * @param string $userName
     * @param string $userPassword
     *
     * @return bool
     */
    protected function _connectToFtp($host, $userName, $userPassword)
    {
        $url = preg_split('/:/i', $host);
        if (count($url) > 1) {
            $host = $url[0];
            $port = $url[1];
        } else {
            $port = null;
        }
        if (is_null(self::$_connId)) {
            self::$_connId = ftp_connect($host, $port);
            if (!self::$_connId) {
                $this->fail('Can not connect to ftp: ' . $host);
            }
        }
        if (is_null(self::$_loginResult)) {
            self::$_loginResult = ftp_login(self::$_connId, $userName, $userPassword);
        }
        if ((!self::$_connId) || (!self::$_loginResult)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Read and fill Ftp parameters
     *
     * @param array $connectionData
     */
    protected function _fillConnectionParameters(array &$connectionData)
    {
        if (isset($connectionData['server_type']) && strtolower($connectionData['server_type']) == 'remote ftp') {
            //Read application config
            $appConfig = $this->getApplicationConfig();
            if (!isset($appConfig['ftp'])) {
                $this->fail('FTP settings are not defined in Config.yml file');
            }
            if (!isset($connectionData['host'])) {
                $connectionData['host'] = $appConfig['ftp']['url'];
            }
            if (!isset($connectionData['file_path'])) {
                $connectionData['file_path'] = $appConfig['ftp']['base_dir'];
            }
            if (!isset($connectionData['user_name'])) {
                $connectionData['user_name'] = $appConfig['ftp']['login'];
            }
            if (!isset($connectionData['password'])) {
                $connectionData['password'] = $appConfig['ftp']['password'];
            }
        }
    }

    /**
     * Get file from FTP server and return file content as string
     *
     * @param string $fileMode
     * @param string $filePath
     * @param string $fileName
     *
     * @return bool|string
     */
    public function getFileFromFtp($fileMode, $filePath, $fileName)
    {
        $temp = tmpfile();
        $result = ftp_fget(self::$_connId, $temp, $filePath . '/' . $fileName, $fileMode);
        if (!$result) {
            return false;
        }
        fseek($temp, 0);
        $fileContent = '';
        while (!feof($temp)) {
            $fileContent .= fread($temp, 1000);
        }
        return $fileContent;
    }

    /**
     * Get Csv file from FTP server and return file content
     *
     * @param array $exportData Information about exported file and job
     *
     * @return array|bool
     */
    public function getCsvFromFtp(array $exportData)
    {
        $this->_fillConnectionParameters($exportData);
        if ($this->_connectToFtp($exportData['host'], $exportData['user_name'], $exportData['password'])) {
            $exportData['file_mode'] = (strtolower($exportData['file_mode']) == 'binary') ? FTP_BINARY : FTP_ASCII;
            $fileContent =
                $this->getFileFromFtp($exportData['file_mode'], $exportData['file_path'], $exportData['file_name']);
            return $this->csvHelper()->csvToArray($fileContent);
        } else {
            return false;
        }
    }

    /**
     * Put file content to FTP server
     *
     * @param string $fileMode
     * @param string $filePath
     * @param string $fileName
     * @param string $fileContent
     *
     * @return bool
     */
    public function putFileToFtp($fileMode, $filePath, $fileName, $fileContent)
    {
        $temp = tmpfile();
        fwrite($temp, $fileContent);
        fseek($temp, 0);
        $result = ftp_fput(self::$_connId, $filePath . '/' . $fileName, $temp, $fileMode);
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Put Csv file content to FTP server
     *
     * @param array $importData
     * @param array $fileContent
     *
     * @return bool
     */
    public function putCsvToFtp(array $importData, array $fileContent)
    {
        $this->_fillConnectionParameters($importData);
        if ($this->_connectToFtp($importData['host'], $importData['user_name'], $importData['password'])) {
            $fileContent = $this->csvHelper()->arrayToCsv($fileContent);
            $importData['file_mode'] = (strtolower($importData['file_mode']) == 'binary') ? FTP_BINARY : FTP_ASCII;
            return $this->putFileToFtp($importData['file_mode'], $importData['file_path'], $importData['file_name'],
                $fileContent);
        } else {
            return false;
        }
    }

    /**
     * Create scheduled export job
     *
     * @param array $exportData
     *
     * @return void
     */
    public function createExport(array &$exportData)
    {
        $this->_fillConnectionParameters($exportData);
        $skipped = array();
        $filters = array();
        $this->addParameter('type', 'Export');
        $this->clickButton('add_scheduled_export');
        if (isset($exportData['skipped'])) {
            $skipped = $exportData['skipped'];
            unset($exportData['skipped']);
        }
        if (isset($exportData['filters'])) {
            $filters = $exportData['filters'];
            unset($exportData['filters']);
        }
        $this->fillForm($exportData);
        foreach ($skipped as $attributeToSkip) {
            $this->importExportHelper()->customerSkipAttribute($attributeToSkip, 'grid_and_filter');
        }
        if (count($filters) > 0) {
            $this->importExportHelper()->setFilter($filters);
        }
        $this->saveForm('save');
    }

    /**
     * Create scheduled import job
     *
     * @param array $importData
     *
     * @return void
     */
    public function createImport(array &$importData)
    {
        $this->_fillConnectionParameters($importData);
        $this->addParameter('type', 'Import');
        $this->clickButton('add_scheduled_import');
        $this->fillForm($importData);
        $this->saveForm('save');
    }

    /**
     * Open scheduled job
     *
     * @param array $searchData
     *
     * @return void
     */
    public function openImportExport(array $searchData)
    {
        $this->addParameter('type', $searchData['operation']);
        $this->searchAndOpen($searchData, 'grid_and_filter', true);
    }

    /**
     * Get current outcome
     *
     * @param array $searchData
     *
     * @return string
     */
    public function getLastOutcome(array $searchData)
    {
        $this->_prepareDataForSearch($searchData);
        $xpath = $this->search($searchData, 'grid_and_filter');
        $columnNumber = $this->getColumnIdByName('Last Outcome', $this->_getControlXpath('field', 'grid'));
        $this->assertNotNull($xpath, 'Can\'t find item in grid for data: ' . print_r($searchData, true));
        return $this->getElementsValue($xpath . "/td[{$columnNumber}]", 'text');
    }

    /**
     * Check if scheduled import/export is present in grid
     *
     * @param array $searchData
     *
     * @return bool
     */
    public function isImportExportPresentInGrid(array $searchData)
    {
        $this->_prepareDataForSearch($searchData);
        $xpath = $this->search($searchData, 'grid_and_filter');
        if (!$xpath) {
            return false;
        }
        return true;
    }

    /**
     * Get last run date
     *
     * @param array $searchData
     *
     * @return string  Date as string in format M j, Y g:i:s A
     */
    public function getLastRunDate(array $searchData)
    {
        $this->_prepareDataForSearch($searchData);
        $xpath = $this->search($searchData, 'grid_and_filter');
        $columnNumber = $this->getColumnIdByName('Last Run Date', $this->_getControlXpath('field', 'grid'));
        $this->assertNotNull($xpath, 'Can\'t find item in grid for data: ' . print_r($searchData, true));
        return $this->getElementsValue($xpath . "/td[{$columnNumber}]", 'text');
    }

    /**
     * Get last file prefix date
     *
     * @param array $searchData
     *
     * @return string  Date as string in format Y-m-d_H-i-s_
     */
    public function getFilePrefix(array $searchData)
    {
        $filePrefix = $this->getLastRunDate($searchData);
        $filePrefix = strtotime($filePrefix);
        $filePrefix = date('Y-m-d_H-i-s_', $filePrefix);
        return $filePrefix;
    }

    /**
     * Apply Action to specific job
     *
     * @param array $searchData
     * @param string $action Run|Edit
     *
     * @return void
     */
    public function applyAction(array $searchData, $action = 'Run')
    {
        $this->_prepareDataForSearch($searchData);
        $xpath = $this->search($searchData, 'grid_and_filter');
        $columnNumber = $this->getColumnIdByName('Action', $this->_getControlXpath('field', 'grid'));
        if ($xpath) {
            $this->fillDropdown('action', $action, $xpath . "/td[{$columnNumber}]/select");
            $this->waitForPageToLoad();
            if (strtolower($action) == 'edit') {
                $this->addParameter('type', $searchData['operation']);
                $this->addParameter('id', $this->defineIdFromUrl());
                $this->checkCurrentPage('scheduled_importexport_edit');
                $this->setCurrentPage('scheduled_importexport_edit');
            }
        } else {
            $this->fail('Can\'t find item in grid for data: ' . print_r($searchData, true));
        }
    }

    /**
     * Searches the specified data in the specific grid. Returns null or XPath of the found data.
     *
     * @param array $data Array of data to look up.
     * @param string|null $fieldSetName Fieldset name that contains the grid (by default = null)
     *
     * @return string|null
     */
    public function searchImportExport(array $data, $fieldSetName = null)
    {
        $xpath = '';
        //$xpathContainer = null;
        if ($fieldSetName) {
            $xpath = $this->_getControlXpath('fieldset', $fieldSetName);
        }
        $qtyElementsInTable = $this->_getControlXpath('pageelement', 'qtyElementsInTable');
        //Forming xpath that contains string 'Total $number records found' where $number - number of items in table
        $totalCount = intval($this->getElement($xpath . $qtyElementsInTable)->text());
        $xpathPager = $xpath . $qtyElementsInTable . "[not(text()='" . $totalCount . "')]";
        $xpathTR = $this->formSearchXpath($data);
        //fill filter
        $this->fillForm($data);
        $this->clickButton('search', false);
        $this->waitForElement($xpathPager);
        if ($this->elementIsPresent($xpath . $xpathTR)) {
            return $xpath . $xpathTR;
        }
        return null;
    }

    /**
     * Delete all scheduled import/export jobs
     */
    public function deleteAllJobs()
    {
        $this->admin('scheduled_import_export');
        if ($this->isImportExportPresentInGrid(array('operation' => 'Export'))
            || $this->isImportExportPresentInGrid(array('operation' => 'Import'))
        ) {
            $this->clickControl('link', 'selectall', false);
            $this->fillDropdown('grid_massaction_select', 'Delete');
            $this->clickButtonAndConfirm('submit', 'delete_confirmation');
        }
    }
}
