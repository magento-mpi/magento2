<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
* Helper class that simplifies gz files stream reading and writing
*/
namespace Magento\Archive\Helper\File;

class Gz extends \Magento\Archive\Helper\File
{
    /**
     * Open gz archive file
     *
     * @param string $mode
     * @return void
     * @throws \Magento\Exception
     */
    protected function _open($mode)
    {
        $this->_fileHandler = @gzopen($this->_filePath, $mode);

        if (false === $this->_fileHandler) {
            throw new \Magento\Exception('Failed to open file ' . $this->_filePath);
        }
    }

    /**
     * Write data to gz archive
     *
     * @param string $data
     * @return void
     * @throws \Magento\Exception
     */
    protected function _write($data)
    {
        $result = @gzwrite($this->_fileHandler, $data);

        if (empty($result) && !empty($data)) {
            throw new \Magento\Exception('Failed to write data to ' . $this->_filePath);
        }
    }

    /**
     * Read data from gz archive
     *
     * @param int $length
     * @return string
     */
    protected function _read($length)
    {
        return gzread($this->_fileHandler, $length);
    }

    /**
     * Gz implementation of EOF indicator
     *
     * @return bool|int
     */
    protected function _eof()
    {
        return gzeof($this->_fileHandler);
    }

    /**
     * Gz implementation of file closing
     *
     * @return void
     */
    protected function _close()
    {
        gzclose($this->_fileHandler);
    }
}
