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
     * Adapter object constructor.
     *
     * @param string $destination OPTIONAL Destination file path.
     * @param bool $createPath Create destination path
     * @throws Exception
     */
    final public function __construct($destination, $createPath = true)
    {
        if ($destination && $createPath) {
            $lib = new Varien_Io_File();
            $lib->setAllowCreateFolders(true)
                ->createDestinationDir(dirname($destination));
        }

        /** @var $helper Mage_ImportExport_Helper_Data */
        $helper = Mage::helper('Saas_ImportExport_Helper_Data');
        if (!$destination) {
            $destination = tempnam(sys_get_temp_dir(), 'importexport_');
        }
        if (!is_string($destination)) {
            Mage::throwException($helper->__('Destination file path must be a string'));
        }
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
    abstract public function writeRow(array $rowData);

    /**
     * Truncate destination dir
     *
     * @return boolean
     */
    abstract public function truncate();

    /**
     * Set column names.
     *
     * @param array $headerColumns
     * @param boolean $writeToFile
     */
    abstract protected function _setHeaderCols(array $headerColumns, $writeToFile = true);

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return 'application/octet-stream';
    }

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
     * Get contents of export file
     *
     * @return string
     */
    public function getContents()
    {
        return file_get_contents($this->_destination);
    }

    /**
     * Return file extension for downloading
     *
     * @return string
     */
    public function getFileExtension()
    {
        return '';
    }

    /**
     * Rename temporary file if it is last task
     *
     * @throws Exception
     * @return Saas_ImportExport_Model_Export_Adapter_Abstract
     */
    public function renameTemporaryFile()
    {
        $destination = $this->_destination . '.' . $this->getFileExtension();
        if (!rename($this->_destination, $destination)) {
            Mage::throwException(
                Mage::helper('Saas_ImportExport_Helper_Data')->__('Temporary export file has not been renamed')
            );
        }
        return $this;
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
