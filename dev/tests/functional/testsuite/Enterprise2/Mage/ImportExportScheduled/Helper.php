<?php
/**
 * Magento
 *
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
class Enterprise2_Mage_ImportExportScheduled_Helper extends Mage_Selenium_TestCase
{
    /**
     * FTP Connection handle
     *
     * @var null
     */
    protected static $connId = NULL;
    /**
     * FTP Login result status
     *
     * @var null
     */
    protected static $loginResult = NULL;

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
        if (is_null(self::$connId)) {
            self::$connId = ftp_connect($host, $port) or die('could not connect to ftp');
        }
        if (is_null(self::$loginResult)) {
            self::$loginResult = ftp_login(self::$connId, $userName, $userPassword);
        }
        if ((!self::$connId) || (!self::$loginResult)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get file from FTP server and return file content as string
     *
     * @param string $fileMode
     * @param string $passive
     * @param string $filePath
     * @param string $fileName
     *
     * @return bool|string
     */
    public function getFileFromFtp($fileMode, $passive, $filePath, $fileName)
    {
        $temp = tmpfile();
        $result = ftp_fget(self::$connId, $temp, $filePath . '/' . $fileName, $fileMode);
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
        if ($this->_connectToFtp($exportData['host'], $exportData['user_name'], $exportData['password'])) {
            $exportData['file_mode'] = (strtolower($exportData['file_mode']) == 'binary') ? FTP_BINARY : FTP_ASCII;
            $fileContent = $this->getFileFromFtp(
                                                 $exportData['file_mode'],
                                                 $exportData['passive_mode'],
                                                 $exportData['file_path'],
                                                 $exportData['file_name']);
            return $this->importExportHelper()->csvToArray($fileContent);
        } else {
            return false;
        }
    }

    /**
     * Put file content to FTP server
     *
     * @param string $fileMode
     * @param string $passive
     * @param string $filePath
     * @param string $fileName
     * @param string $fileContent
     *
     * @return bool
     */
    public function putFileToFtp($fileMode, $passive, $filePath, $fileName, $fileContent)
    {
        $temp = tmpfile();
        fwrite($temp, $fileContent);
        fseek($temp, 0);
        $result = ftp_fput(self::$connId, $filePath . '/' . $fileName, $temp, $fileMode);
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
     * @param string $fileContent
     *
     * @return bool
     */
    public function putCsvToFtp(array $importData, array $fileContent)
    {
        if ($this->_connectToFtp($importData['host'], $importData['user_name'], $importData['password'])) {
            $fileContent = $this->importExportHelper()->arrayToCsv($fileContent);
            $importData['file_mode'] = (strtolower($importData['file_mode']) == 'binary') ? FTP_BINARY : FTP_ASCII;
            return $this->putFileToFtp(
                                        $importData['file_mode'],
                                        $importData['passive_mode'],
                                        $importData['file_path'],
                                        $importData['file_name'],
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
    public function createExport(array $exportData)
    {
        $skipped = array();
        $filters = array();
        $this->addParameter('type', 'Export');
        $this->clickButton('add_scheduled_export');
        if (isset($exportData['skipped'])){
            $skipped = $exportData['skipped'];
            unset($exportData['skipped']);
        }
        if (isset($exportData['filters'])){
            $filters = $exportData['filters'];
            unset($exportData['filters']);
        }
        $this->fillForm($exportData);
        foreach ($skipped as $attributeToSkip){
             $this->importExportHelper()->customerSkipAttribute($attributeToSkip, 'grid_and_filter');
        }
        if (count($filters)>0){
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
    public function createImport(array $importData)
    {
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
        $this->searchAndOpen($searchData, true, 'grid_and_filter');
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
        $columnNumber =  $this->getColumnIdByName('Last Outcome',
            $this->_getControlXpath('field', 'grid'));
        if ($xpath){
            return $this->getElementByXpath($xpath . "/td[{$columnNumber}]");
        } else {
            $this->fail('Can\'t find item in grid for data: ' . print_r($searchData, true));
        }
    }
    /**
     * Check if scheduled import/export is present in grid
     *
     * @param array $searchData
     * @return bool
     */
    public function isImportExportPresentInGrid(array $searchData)
    {
        $this->_prepareDataForSearch($searchData);
        $xpath = $this->search($searchData, 'grid_and_filter');
        if (!is_null($xpath)) {
            return true;
        } else {
            return false;
        }
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
        $columnNumber =  $this->getColumnIdByName('Last Run Date',
                                                  $this->_getControlXpath('field', 'grid'));
        if ($xpath){
            return $this->getElementByXpath($xpath . "/td[{$columnNumber}]");
        } else {
            $this->fail('Can\'t find item in grid for data: ' . print_r($searchData, true));
        }
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
     * @param string $action
     *
     * @return void
     */
    public function applyAction(array $searchData, $action = 'Run')
    {
        $this->_prepareDataForSearch($searchData);
        $xpath = $this->search($searchData, 'grid_and_filter');
        $columnNumber =  $this->getColumnIdByName('Action',
            $this->_getControlXpath('field', 'grid'));
        if ($xpath){
            $this->fillDropdown('action', $action, $xpath . "/td[{$columnNumber}]/select");
            $this->waitForPageToLoad();
        } else {
            $this->fail('Can\'t find item in grid for data: ' . print_r($searchData, true));
        }
    }
}
