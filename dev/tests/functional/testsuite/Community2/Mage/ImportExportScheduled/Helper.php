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
class Community2_Mage_ScheduledImportExport_Helper extends Mage_Selenium_TestCase
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
        $result = ftp_fget(self::$connId, $temp, $filePath . $fileName, $fileMode);
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
     * @param string $host
     * @param string $userName
     * @param string $userPassword
     * @param string $fileMode
     * @param string $passive
     * @param string $filePath
     * @param string $fileName
     *
     * @return array|bool
     */
    public function getCsvFromFtp($host, $userName, $userPassword, $fileMode, $passive, $filePath, $fileName)
    {
        if ($this->_connectToFtp($host, $userName, $userPassword)) {
            $fileContent = $this->getFileFromFtp($fileMode, $passive, $filePath, $fileName);
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
        $result = ftp_fput(self::$connId, $filePath . $fileName, $temp, $fileMode);
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Put Csv file content to FTP server
     *
     * @param string $host
     * @param string $userName
     * @param string $userPassword
     * @param string $fileMode
     * @param string $passive
     * @param string $filePath
     * @param string $fileName
     * @param string $fileContent
     *
     * @return bool
     */
    public function putCsvToFtp($host, $userName, $userPassword, $fileMode, $passive, $filePath, $fileName, $fileContent)
    {
        if ($this->_connectToFtp($host, $userName, $userPassword)) {
            $fileContent = $this->importExportHelper()->arrayToCsv($fileContent);
            return $this->putFileToFtp($fileMode, $passive, $filePath, $fileName, $fileContent);
        } else {
            return false;
        }
    }
}
