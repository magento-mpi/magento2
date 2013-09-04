<?php
/**
 * {license_notice}
 *
 * @category    Magento * @package     \Magento\Archive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
* Helper class that simplifies gz files stream reading and writing
*
* @category    Magento* @package     \Magento\Archive
* @author      Magento Core Team <core@magentocommerce.com>
*/
namespace Magento\Archive\Helper\File;

class Gz extends \Magento\Archive\Helper\File
{
    /**
     * @see \Magento\Archive\Helper\File::_open()
     */
    protected function _open($mode)
    {
        $this->_fileHandler = @gzopen($this->_filePath, $mode);

        if (false === $this->_fileHandler) {
            throw new \Magento\Exception('Failed to open file ' . $this->_filePath);
        }
    }

    /**
     * @see \Magento\Archive\Helper\File::_write()
     */
    protected function _write($data)
    {
        $result = @gzwrite($this->_fileHandler, $data);

        if (empty($result) && !empty($data)) {
            throw new \Magento\Exception('Failed to write data to ' . $this->_filePath);
        }
    }

    /**
     * @see \Magento\Archive\Helper\File::_read()
     */
    protected function _read($length)
    {
        return gzread($this->_fileHandler, $length);
    }

    /**
     * @see \Magento\Archive\Helper\File::_eof()
     */
    protected function _eof()
    {
        return gzeof($this->_fileHandler);
    }

    /**
     * @see \Magento\Archive\Helper\File::_close()
     */
    protected function _close()
    {
        gzclose($this->_fileHandler);
    }
}
