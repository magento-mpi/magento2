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
     * @var null|bool|Varien_Object
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
        'csv' => 'text/csv',
    );

    /**
     * Default file mime type
     */
    const MIME_TYPE_DEFAULT = 'application/octet-stream';

    /**
     * Constructor
     *
     * @param Mage_Core_Helper_Context $context
     * @param Saas_ImportExport_Helper_Export_Config $configHelper
     * @param Saas_ImportExport_Model_Export_State_FlagFactory $flagFactory
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Saas_ImportExport_Helper_Export_Config $configHelper,
        Saas_ImportExport_Model_Export_State_FlagFactory $flagFactory,
        Magento_Filesystem $filesystem
    ) {
        $this->_configHelper = $configHelper;
        $this->_stateFlag = $flagFactory->create();
        $this->_filesystem = $filesystem;
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
        return isset($this->_mimeTypes[$extension]) ? $this->_mimeTypes[$extension] : self::MIME_TYPE_DEFAULT;
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
     * Retrieve last export file information or false if not exists
     *
     * @return bool|Varien_Object
     */
    protected function _getFile()
    {
        if (is_null($this->_file)) {
            $exportFile = $this->_stateFlag->getExportFilename();
            if (!$exportFile || !$this->_filesystem->isFile($exportFile)) {
                $this->_file = false;
                return false;
            }
            // Magento_Filesystem currently don't have this functionality
            $fileInfo = pathinfo($exportFile);
            $extension = $fileInfo['extension'];
            $this->_file = new Varien_Object(array(
                'path' => $exportFile,
                'download_name' => $fileInfo['filename']
                    . date('_Ymd_His', $this->_filesystem->getMTime($exportFile))
                    . '.' . $extension,
                'extension' => $extension
            ));
        }
        return $this->_file;
    }
}
