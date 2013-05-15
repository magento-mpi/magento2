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
        $this->_fileHandler = fopen($this->_destination, 'a+');
        return $this;
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
    public function writeRow($rowData)
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
     * Set column names.
     *
     * @param array $headerColumns
     * @param boolean $writeToFile
     * @throws Exception
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _setHeaderCols(array $headerColumns, $writeToFile = true)
    {
        if (null !== $this->_headerCols && $writeToFile) {
            return $this;
        }
        if ($headerColumns) {
            foreach ($headerColumns as $columnName) {
                $this->_headerCols[$columnName] = false;
            }
            if ($writeToFile) {
                fputcsv(
                    $this->_fileHandler,
                    array_keys($this->_headerCols),
                    $this->_delimiter,
                    $this->_enclosure
                );
            }
        }
        return $this;
    }
}
