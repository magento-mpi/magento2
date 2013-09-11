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
namespace Magento\ImportExport\Model\Export\Adapter;

abstract class AbstractAdapter
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
     * @throws \Exception
     */
    final public function __construct($destination = null)
    {
        /** @var $helper \Magento\ImportExport\Helper\Data */
        $helper = \Mage::helper('Magento\ImportExport\Helper\Data');
        if (!$destination) {
            $destination = tempnam(sys_get_temp_dir(), 'importexport_');
        }
        if (!is_string($destination)) {
            \Mage::throwException(__('Destination file path must be a string'));
        }
        $pathinfo = pathinfo($destination);

        if (empty($pathinfo['dirname']) || !is_writable($pathinfo['dirname'])) {
            \Mage::throwException(__('Destination directory is not writable'));
        }
        if (is_file($destination) && !is_writable($destination)) {
            \Mage::throwException(__('Destination file is not writable'));
        }
        $this->_destination = $destination;

        $this->_init();
    }

    /**
     * Method called as last step of object instance creation. Can be overridden in child classes.
     *
     * @return \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter
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
     * @return \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter
     */
    public function setHeaderCols(array $headerColumns)
    {
        return $this;
    }

    /**
     * Write row data to source file
     *
     * @param array $rowData
     * @return \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter
     */
    abstract public function writeRow(array $rowData);
}
