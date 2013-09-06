<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class for ftp loader which using in the Rest
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class \Magento\Connect\Loader\Ftp
{

    const TEMPORARY_DIR = '../var/package/tmp';

    const FTP_USER = 'anonymous';

    const FTP_PASS = 'test@gmail.com';

    /**
    * Object of Ftp
    *
    * @var \Magento\Connect\Ftp
    */
    protected $_ftp = null;

    /**
     * User name
     * @var string
     */
    protected $_ftpUser = '';

    /**
     * User password
     * @var string
     */
    protected $_ftpPassword = '';

    /**
     * Response body
     * @var string
     */
    protected $_responseBody = '';

    /**
     * Response status
     * @var int
     */
    protected $_responseStatus = 0;

    /**
    * Constructor
    */
    public function __construct()
    {
        $this->_ftp = new \Magento\Connect\Ftp();
        $this->_ftpUser = self::FTP_USER;
        $this->_ftpPassword = self::FTP_PASS;
    }

    public function getFtp()
    {
        return $this->_ftp;
    }

    /**
    * Retrieve file from URI
    *
    * @param mixed $uri
    * @return bool
    */
    public function get($uri)
    {
        $remoteFile = basename($uri);
        $uri = dirname($uri);
        $uri = str_replace('http://', '', $uri);
        $uri = str_replace('https://', '', $uri);
        $uri = str_replace('ftp://', '', $uri);
        $uri = $this->_ftpUser.":".$this->_ftpPassword."@".$uri;
        $this->getFtp()->connect("ftp://".$uri);
        $this->getFtp()->pasv(true);
        $tmpDir = self::TEMPORARY_DIR . DS;
        if (!is_dir($tmpDir)) {
            $tmpDir = sys_get_temp_dir();
        }
        if (substr($tmpDir, -1) != DS) {
            $tmpDir .= DS;
        }
        $localFile = $tmpDir . time() . ".xml";

        if ($this->getFtp()->get($localFile, $remoteFile)) {
            $this->_responseBody = file_get_contents($localFile);
            $this->_responseStatus = 200;
        }
        @unlink($localFile);
        $this->getFtp()->close();
        return $this;
    }

    /**
     * Get response status code
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_responseStatus;
    }

    /**
    * put your comment there...
    *
    * @return string
    */
    public function getBody()
    {
        return $this->_responseBody;
    }

    /**
    * Set login credentials for ftp auth.
    * @param string $ftpLogin Ftp User account name
    * @param string $ftpPassword User password
    * @return string
    */
    public function setCredentials($ftpLogin, $ftpPassword)
    {
        $this->_ftpUser = $ftpLogin;
        $this->_ftpPassword = $ftpPassword;
    }

}
