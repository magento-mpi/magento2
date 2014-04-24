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
* Helper class that simplifies gz files stream reading and writing
*
* @category    Magento
* @package     Magento_Archive
* @author      Magento Core Team <core@magentocommerce.com>
*/
class Gz extends \Magento\Framework\Archive\Helper\File
{
    /**
     * @param string $mode
     * @return void
     * @see \Magento\Framework\Archive\Helper\File::_open()
     */
    protected function _open($mode)
    {
        $this->_fileHandler = @gzopen($this->_filePath, $mode);

        if (false === $this->_fileHandler) {
            throw new \Magento\Framework\Exception('Failed to open file ' . $this->_filePath);
        }
    }

    /**
     * @param string $data
     * @return void
     * @see \Magento\Framework\Archive\Helper\File::_write()
     */
    protected function _write($data)
    {
        $result = @gzwrite($this->_fileHandler, $data);

        if (empty($result) && !empty($data)) {
            throw new \Magento\Framework\Exception('Failed to write data to ' . $this->_filePath);
        }
    }

    /**
     * @param int $length
     * @return string
     * @see \Magento\Framework\Archive\Helper\File::_read()
     */
    protected function _read($length)
    {
        return gzread($this->_fileHandler, $length);
    }

    /**
     * @return int|false
     * @see \Magento\Framework\Archive\Helper\File::_eof()
     */
    protected function _eof()
    {
        return gzeof($this->_fileHandler);
    }

    /**
     * @return void
     * @see \Magento\Framework\Archive\Helper\File::_close()
     */
    protected function _close()
    {
        gzclose($this->_fileHandler);
    }
}
