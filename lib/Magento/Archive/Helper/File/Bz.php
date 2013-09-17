<?php
/**
 * {license_notice}
 *
 * @category    Magento * @package     Magento_Archive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
* Helper class that simplifies bz2 files stream reading and writing
*
* @category    Magento* @package     Magento_Archive
* @author      Magento Core Team <core@magentocommerce.com>
*/
class Magento_Archive_Helper_File_Bz extends Magento_Archive_Helper_File
{
    /**
     * Open bz archive file
     *
     * @throws Magento_Exception
     * @param string $mode
     */
    protected function _open($mode)
    {
        $this->_fileHandler = @bzopen($this->_filePath, $mode);

        if (false === $this->_fileHandler) {
            throw new Magento_Exception('Failed to open file ' . $this->_filePath);
        }
    }

    /**
     * Write data to bz archive
     * 
     * @throws Magento_Exception
     * @param $data
     */
    protected function _write($data)
    {
        $result = @bzwrite($this->_fileHandler, $data);

        if (false === $result) {
            throw new Magento_Exception('Failed to write data to ' . $this->_filePath);
        }
    }

    /**
     * Read data from bz archive
     *
     * @throws Magento_Exception
     * @param int $length
     * @return string
     */
    protected function _read($length)
    {
        $data = bzread($this->_fileHandler, $length);

        if (false === $data) {
            throw new Magento_Exception('Failed to read data from ' . $this->_filePath);
        }

        return $data;
    }

    /**
     * Close bz archive
     */
    protected function _close()
    {
        bzclose($this->_fileHandler);
    }
}

