<?php
/**
 * Saas Export File Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Export_File extends Mage_Core_Helper_Abstract
{
    /**#@+
     * Mime type constants
     */
    const MIME_TYPE_DEFAULT = 'application/octet-stream';
    const MIME_TYPE_CSV = 'text/csv';
    /**#@-*/

    /**
     * @var Saas_ImportExport_Helper_Export_Config
     */
    protected $_configHelper;

    /**
     * @var Saas_ImportExport_Model_Export_State_Flag
     */
    protected $_stateFlag;

    /**
     * @var null|array
     */
    protected $_file = null;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * List of available mime-types
     *
     * @var array
     */
    protected $_mimeTypes = array(
        Saas_ImportExport_Model_Export_Adapter_Csv::EXTENSION_CSV => self::MIME_TYPE_CSV,
    );

    /**
     * Constructor
     *
     * @param Mage_Core_Helper_Context $context
     * @param Saas_ImportExport_Helper_Export_Config $configHelper
     * @param Saas_ImportExport_Model_Export_State_Flag $stateFlag
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Saas_ImportExport_Helper_Export_Config $configHelper,
        Saas_ImportExport_Model_Export_State_Flag $stateFlag,
        Magento_Filesystem $filesystem
    ) {
        $this->_configHelper = $configHelper;
        $this->_stateFlag = $stateFlag;
        $this->_filesystem = $filesystem;
        $this->_initFile();
        parent::__construct($context);
    }

    /**
     * Get export file name for download
     *
     * @return string
     */
    public function getDownloadName()
    {
        return $this->_getFileParam('download_name');
    }

    /**
     * Get export file mime type
     *
     * @return string
     */
    public function getMimeType()
    {
        $extension = $this->_getFileParam('extension');
        return isset($this->_mimeTypes[$extension]) ? $this->_mimeTypes[$extension] : self::MIME_TYPE_DEFAULT;
    }

    /**
     * Get absolute path for export file
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_getFileParam('path');
    }

    /**
     * Is export file exist
     *
     * @return bool
     */
    public function isExist()
    {
        return (bool)$this->_file;
    }

    /**
     * Remove last export file
     *
     * @return Saas_ImportExport_Helper_Export_File
     * @throws Magento_Filesystem_Exception
     */
    public function removeLastExportFile()
    {
        if ($this->isExist()) {
            $this->_filesystem->delete($this->_stateFlag->getExportFilename());
        }
        return $this;
    }

    /**
     * Retrieve export file parameter
     *
     * @param string $param
     * @return string
     */
    protected function _getFileParam($param)
    {
        return $this->isExist() ? $this->_file[$param] : '';
    }

    /**
     * Init last export file information
     *
     * @return bool|Varien_Object
     */
    protected function _initFile()
    {
        $exportFile = $this->_stateFlag->getExportFilename();
        if ($exportFile && $this->_filesystem->isFile($exportFile, $this->_configHelper->getStorageDirectoryPath())) {
            // Magento_Filesystem currently don't have this functionality
            $fileInfo = pathinfo($exportFile);
            $extension = $fileInfo['extension'];
            $this->_file = array(
                'path' => $exportFile,
                'download_name' => $fileInfo['filename']
                    . date('_Ymd_His', $this->_filesystem->getMTime($exportFile))
                    . '.' . $extension,
                'extension' => $extension
            );
        }
    }
}
