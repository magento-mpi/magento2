<?php
/**
 * Abstract adapter model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Saas_ImportExport_Model_Export_Adapter_Abstract
{
    /**
     * Destination file path.
     *
     * @var string
     */
    protected $_destination;

    /**
     * Header columns names.
     *
     * @var array
     */
    protected $_headerCols = null;

    /**
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    /**
     * Source file handler.
     *
     * @var resource
     */
    protected $_fileHandler;

    /**
     * Adapter object constructor.
     *
     * @param string $destination
     * @param Saas_ImportExport_Helper_Data $helper
     * @param bool $createPath
     * @throws Exception
     */
    final public function __construct($destination, Saas_ImportExport_Helper_Data $helper, $createPath = true)
    {
        if ($createPath) {
            $lib = new Varien_Io_File();
            $lib->setAllowCreateFolders(true)->createDestinationDir(dirname($destination));
        }
        $this->_helper = $helper;
        $pathinfo = pathinfo($destination);

        if (empty($pathinfo['dirname']) || !is_writable($pathinfo['dirname'])) {
            Mage::throwException($helper->__('Destination directory is not writable'));
        }
        if (is_file($destination) && !is_writable($destination)) {
            Mage::throwException($helper->__('Destination file is not writable'));
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
    abstract public function writeRow($rowData);

    /**
     * Set column names.
     *
     * @param array $headerColumns
     * @param boolean $writeToFile
     */
    abstract protected function _setHeaderCols(array $headerColumns, $writeToFile = true);

    /**
     * Return file extension for downloading
     *
     * @return string
     */
    abstract public function getFileExtension();

    /**
     * Set column names.
     *
     * @param array $headerCols
     * @return Saas_ImportExport_Model_Export_Adapter_Abstract
     */
    public function setHeaderCols(array $headerCols)
    {
        $this->_setHeaderCols($headerCols);
        return $this;
    }

    /**
     * Set column names without write to file
     *
     * @param array $headerCols
     * @return Saas_ImportExport_Model_Export_Adapter_Abstract
     */
    public function setHeaderColsData(array $headerCols)
    {
        $this->_setHeaderCols($headerCols, false);
        return $this;
    }

    /**
     * Rename temporary file and return path to them
     *
     * @throws Exception
     * @return string
     */
    public function renameTemporaryFile()
    {
        $destination = $this->_destination . '.' . $this->getFileExtension();
        if (!rename($this->_destination, $destination)) {
            Mage::throwException(
                $this->_helper->__('Temporary export file has not been renamed')
            );
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
            $exportFiles = glob('{' . dirname($this->_destination) . DS . '*' . '}', GLOB_BRACE);
            foreach ($exportFiles as $exportFile) {
                if (!unlink($exportFile)) {
                    return false;
                }
            }
            $this->_fileHandler = fopen($this->_destination, 'a+');
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
        return true;
    }

    /**
     * Method called as last step of object instance creation. Can be overridden in child classes.
     *
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _init()
    {
        return $this;
    }
}
