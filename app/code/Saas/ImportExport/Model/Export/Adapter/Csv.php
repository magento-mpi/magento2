<?php
/**
 * Export adapter csv
 *
 * {license_notice}
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export_Adapter_Csv extends Saas_ImportExport_Model_Export_Adapter_AdapterAbstract
{
    /**
     * Field delimiter
     *
     * @var string
     */
    protected $_delimiter = ',';

    /**
     * Field enclosure character
     *
     * @var string
     */
    protected $_enclosure = '"';

    /**
     * Object destructor
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
     * Open file handler
     *
     * @return Saas_ImportExport_Model_Export_Adapter_Csv
     */
    protected function _init()
    {
        $this->_fileHandler = fopen($this->_destination, 'a+');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtension()
    {
        return 'csv';
    }

    /**
     * {@inheritdoc}
     */
    public function writeRow(array $rowData)
    {
        $this->_saveToCsv(array_merge($this->_headerColumns, array_intersect_key($rowData, $this->_headerColumns)));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function writeHeaderColumns(array $headerColumns)
    {
        if (null !== $this->_headerColumns) {
            return $this;
        }
        $this->saveHeaderColumns($headerColumns);
        $this->_saveToCsv(array_keys($this->_headerColumns));
        return $this;
    }

    /**
     * Save data to csv
     *
     * @param array $fields
     */
    protected function _saveToCsv(array $fields)
    {
        fputcsv($this->_fileHandler, $fields, $this->_delimiter, $this->_enclosure);
    }
}
