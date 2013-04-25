<?php
/**
 * Export adapter csv
 *
 * {license_notice}
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export_Adapter_Csv extends Saas_ImportExport_Model_Export_Adapter_Abstract
{
    /**
     * Field delimiter.
     *
     * @var string
     */
    protected $_delimiter = ',';

    /**
     * Field enclosure character.
     *
     * @var string
     */
    protected $_enclosure = '"';

    /**
     * Source file handler.
     *
     * @var resource
     */
    protected $_fileHandler;

    /**
     * Object destructor.
     *
     * @return void
     */
    public function __destruct()
    {
        if (is_resource($this->_fileHandler)) {
            fclose($this->_fileHandler);
        }
    }

    /**
     * Method called as last step of object instance creation. Can be overrided in child classes.
     *
     * @return Saas_ImportExport_Model_Export_Adapter_Csv
     */
    protected function _init()
    {
        $this->_destination = $this->_destination . '.' . $this->getFileExtension();
        if (is_file($this->_destination) && !is_writable($this->_destination)) {
            Mage::throwException(Mage::helper('Saas_ImportExport_Helper_Data')->__('Destination file is not writable'));
        }
        $this->_fileHandler = fopen($this->_destination, 'a+');
        return $this;
    }

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return 'text/csv';
    }

    /**
     * Return file extension for downloading.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return 'csv';
    }

    /**
     * Write row data to source file.
     *
     * @param array $rowData
     * @throws Exception
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    public function writeRow(array $rowData)
    {
        if (null === $this->_headerCols) {
            $this->setHeaderCols(array_keys($rowData));
        }
        fputcsv(
            $this->_fileHandler,
            array_merge($this->_headerCols, array_intersect_key($rowData, $this->_headerCols)),
            $this->_delimiter,
            $this->_enclosure
        );

        return $this;
    }

    /**
     *
     * Truncate destination dir
     *
     * @return boolean
     */
    public function truncate()
    {
        try {
            if (is_resource($this->_fileHandler)) {
                fclose($this->_fileHandler);
            }

            //Remove previous exports
            $exportFiles = glob('{' . dirname($this->_destination) . DS . '*.' . $this->getFileExtension() . '}', GLOB_BRACE);
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
}
