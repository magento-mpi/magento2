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
    /**
     * @var Saas_ImportExport_Helper_Export_Config
     */
    protected $_configHelper;

    /**
     * @var Saas_ImportExport_Model_Export_State_Flag
     */
    protected $_stateFlag;

    /**
     * @var bool|Varien_Object
     */
    protected $_file = null;

    /**
     * List of available mime-types
     *
     * @var array
     */
    protected $_mimeTypes = array(
        'csv' => 'text/csv',
    );

    /**#@+
     * Default file mime type
     */
    const DEFAULT_MIME_TYPE = 'application/octet-stream';
    /**#@-*/

    /**
     * Constructor
     *
     * @param Saas_ImportExport_Helper_Export_Config $configHelper
     * @param Saas_ImportExport_Model_Export_State_FlagFactory $flagFactory
     * @param Mage_Core_Helper_Context $context
     */
    public function __construct(
        Saas_ImportExport_Helper_Export_Config $configHelper,
        Saas_ImportExport_Model_Export_State_FlagFactory $flagFactory,
        Mage_Core_Helper_Context $context
    ) {
        $this->_configHelper = $configHelper;
        $this->_stateFlag = $flagFactory->create();
        parent::__construct($context);
    }

    /**
     * Get export file name for download
     *
     * @return string
     */
    public function getDownloadName()
    {
        return $this->isExist() ? $this->_getFile()->getDownloadName() : '';
    }

    /**
     * Get export file mime type
     *
     * @return string
     */
    public function getMimeType()
    {
        $extension = $this->isExist() ? $this->_getFile()->getExtension() : '';
        return isset($this->_mimeTypes[$extension]) ? $this->_mimeTypes[$extension] : self::DEFAULT_MIME_TYPE;
    }

    /**
     * Get absolute path for export file
     *
     * @return string
     */
    public function getPath()
    {
        return $this->isExist() ? $this->_getFile()->getPath() : '';
    }

    /**
     * Is export file exist
     *
     * @return bool
     */
    public function isExist()
    {
        return (bool)$this->_getFile();
    }

    /**
     * Remove last export file
     *
     * @return Saas_ImportExport_Helper_Export_File
     */
    public function removeLastExportFile()
    {
        $exportFile = $this->_stateFlag->getExportFilename();
        if ($exportFile && file_exists($exportFile) && !unlink($exportFile)) {
            Mage::throwException($this->__('File has not been removed'));
        }
        return $this;
    }

    /**
     * Retrieve last export file information or false if not exists
     *
     * @return bool|Varien_Object
     */
    protected function _getFile()
    {
        if (is_null($this->_file)) {
            $exportFile = $this->_stateFlag->getExportFilename();
            if (!$exportFile || !file_exists($exportFile)) {
                return false;
            }
            $fileInfo = pathinfo($exportFile);
            $dateSuffix = date('_Ymd_His', filemtime($exportFile));
            $extension = $fileInfo['extension'];
            $this->_file = new Varien_Object(array(
                'path' => $exportFile,
                'download_name' => $fileInfo['filename'] . $dateSuffix . '.' . $extension,
                'extension' => $extension
            ));
        }
        return $this->_file;
    }
}
