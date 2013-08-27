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
 * Abstract adapter model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_ImportExport_Model_Export_Adapter_Abstract
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
     * @throws Exception
     */
    final /**
     * Adapter object constructor.
     *
     *
     *
     * @param Magento_ImportExport_Helper_Data $importExportData
     * @param string $destination
     */
    /**
     * Import export data
     *
     * @var Magento_ImportExport_Helper_Data
     */
    protected $_importExportData = null;

    public function __construct(
        Magento_ImportExport_Helper_Data $importExportData,
        $destination = null
    ) {
        $this->_importExportData = $importExportData;
        /** @var $helper Magento_ImportExport_Helper_Data */
        $helper = $this->_importExportData;
        if (!$destination) {
            $destination = tempnam(sys_get_temp_dir(), 'importexport_');
        }
        if (!is_string($destination)) {
            Mage::throwException(__('Destination file path must be a string'));
        }
        $pathinfo = pathinfo($destination);

        if (empty($pathinfo['dirname']) || !is_writable($pathinfo['dirname'])) {
            Mage::throwException(__('Destination directory is not writable'));
        }
        if (is_file($destination) && !is_writable($destination)) {
            Mage::throwException(__('Destination file is not writable'));
        }
        $this->_destination = $destination;

        $this->_init();
    }

    /**
     * Method called as last step of object instance creation. Can be overridden in child classes.
     *
     * @return Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _init()
    {
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
     * MIME-type for 'Content-Type' header
     *
     * @return string
     */
    public function getContentType()
    {
        return 'application/octet-stream';
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
     * Set column names
     *
     * @param array $headerColumns
     * @return Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    public function setHeaderCols(array $headerColumns)
    {
        return $this;
    }

    /**
     * Write row data to source file
     *
     * @param array $rowData
     * @return Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    abstract public function writeRow(array $rowData);
}
