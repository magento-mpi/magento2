<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export adapter csv.
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Export_Adapter_Csv extends Magento_ImportExport_Model_Export_Adapter_Abstract
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
     * @return Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _init()
    {
        $this->_fileHandler = fopen($this->_destination, 'w');
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
     * Set column names.
     *
     * @param array $headerColumns
     * @throws Exception
     * @return Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    public function setHeaderCols(array $headerColumns)
    {
        if (null !== $this->_headerCols) {
            Mage::throwException(__('Header column names already set'));
        }
        if ($headerColumns) {
            foreach ($headerColumns as $columnName) {
                $this->_headerCols[$columnName] = false;
            }
            fputcsv($this->_fileHandler, array_keys($this->_headerCols), $this->_delimiter, $this->_enclosure);
        }
        return $this;
    }

    /**
     * Write row data to source file.
     *
     * @param array $rowData
     * @throws Exception
     * @return Magento_ImportExport_Model_Export_Adapter_Abstract
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
}
