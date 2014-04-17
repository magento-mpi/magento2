<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Archive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Archive\Helper\File;

/**
* Helper class that simplifies bz2 files stream reading and writing
*
* @category    Magento
* @package     Magento_Archive
* @author      Magento Core Team <core@magentocommerce.com>
*/
class Bz extends \Magento\Framework\Archive\Helper\File
{
    /**
     * Open bz archive file
     *
     * @param string $mode
     * @return void
     * @throws \Magento\Framework\Exception
     */
    protected function _open($mode)
    {
        $this->_fileHandler = @bzopen($this->_filePath, $mode);

        if (false === $this->_fileHandler) {
            throw new \Magento\Framework\Exception('Failed to open file ' . $this->_filePath);
        }
    }

    /**
     * Write data to bz archive
     *
     * @param string $data
     * @return void
     * @throws \Magento\Framework\Exception
     */
    protected function _write($data)
    {
        $result = @bzwrite($this->_fileHandler, $data);

        if (false === $result) {
            throw new \Magento\Framework\Exception('Failed to write data to ' . $this->_filePath);
        }
    }

    /**
     * Read data from bz archive
     *
     * @param int $length
     * @return string
     * @throws \Magento\Framework\Exception
     */
    protected function _read($length)
    {
        $data = bzread($this->_fileHandler, $length);

        if (false === $data) {
            throw new \Magento\Framework\Exception('Failed to read data from ' . $this->_filePath);
        }

        return $data;
    }

    /**
     * Close bz archive
     *
     * @return void
     */
    protected function _close()
    {
        bzclose($this->_fileHandler);
    }
}
