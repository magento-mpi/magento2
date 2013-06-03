<?php
/**
 * Abstract adapter model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Saas_ImportExport_Model_Export_Adapter_AdapterAbstract
{
    /**
     * Destination file path
     *
     * @var string
     */
    protected $_destination;

    /**
     * Header columns names
     *
     * @var null|array
     */
    protected $_headerColumns = null;

    /**
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    /**
     * Source file handler
     *
     * @var resource
     */
    protected $_fileHandler;

    /**
     * @var Mage_Core_Model_Logger
     */
    protected $_logger;


    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Adapter object constructor
     *
     * @param string $destination
     * @param Saas_ImportExport_Helper_Data $helper
     * @param Mage_Core_Model_Logger $logger
     * @param Magento_Filesystem $filesystem
     * @param bool $createPath
     * @throws Magento_Filesystem_Exception
     */
    final public function __construct(
        $destination,
        Saas_ImportExport_Helper_Data $helper,
        Mage_Core_Model_Logger $logger,
        Magento_Filesystem $filesystem,
        $createPath = true
    ) {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_filesystem = $filesystem;
        if ($createPath) {
            $directory = dirname($destination);
            $this->_filesystem->setIsAllowCreateDirectories(true);
            // trick for Magento_Filesystem_Exception::isPathInDirectory
            $this->_filesystem->ensureDirectoryExists($directory, 0777, dirname($directory));
            $this->_filesystem->setIsAllowCreateDirectories(false);
            $this->_filesystem->setWorkingDirectory($directory);
        }
        if (!$this->_filesystem->isDirectory(dirname($destination))) {
            throw new Magento_Filesystem_Exception($helper->__('Destination directory is not writable'));
        }
        $this->_destination = $destination;
        $this->_init();
    }

    /**
     * Write row data to source file
     *
     * @param array $rowData
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    abstract public function writeRow(array $rowData);

    /**
     * Return file extension for downloading
     *
     * @return string
     */
    abstract public function getFileExtension();

    /**
     * Write header columns to source file
     *
     * @param array $headerColumns
     * @return Saas_ImportExport_Model_Export_Adapter_AdapterAbstract
     */
    abstract public function writeHeaderColumns(array $headerColumns);

    /**
     * Save header columns to $_headerColumns property
     *
     * @param array $headerColumns
     * @return Saas_ImportExport_Model_Export_Adapter_AdapterAbstract
     */
    public function saveHeaderColumns(array $headerColumns)
    {
        $this->_headerColumns = array();
        foreach ($headerColumns as $columnName) {
            $this->_headerColumns[$columnName] = false;
        }
        return $this;
    }

    /**
     * Rename temporary file and return path to them
     *
     * @throws Magento_Filesystem_Exception
     * @return string
     */
    public function renameTemporaryFile()
    {
        $destination = $this->_destination . '.' . $this->getFileExtension();
        if (!$this->_filesystem->rename($this->_destination, $destination)) {
            throw new Magento_Filesystem_Exception($this->_helper->__('Temporary export file has not been renamed'));
        }
        return $destination;
    }

    /**
     * Cleanup destination dir
     *
     * @return boolean
     */
    public function cleanupWorkingDir()
    {
        try {
            if (is_resource($this->_fileHandler)) {
                fclose($this->_fileHandler);
            }
            // Remove previous exports
            $exportFiles = $this->_filesystem->searchKeys(dirname($this->_destination), '*');
            foreach ($exportFiles as $exportFile) {
                if (!$this->_filesystem->delete($exportFile)) {
                    $this->_logger->log(sprintf('Export cleanup dir error in %s. File %', __METHOD__, $exportFile));
                }
            }
        } catch (Exception $e) {
            $this->_logger->logException($e);
        }
        $this->_fileHandler = fopen($this->_destination, 'a+');
        return true;
    }

    /**
     * Method called as last step of object instance creation. Can be overridden in child classes
     *
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _init()
    {
        return $this;
    }
}
