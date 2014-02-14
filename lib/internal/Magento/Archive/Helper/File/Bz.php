<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class that simplifies bz2 files stream reading and writing
 */
namespace Magento\Archive\Helper\File;

class Bz extends \Magento\Archive\Helper\File
{
    /**
     * {@inheritdoc}
     */
    protected function _open($mode)
    {
        $this->_fileHandler = @bzopen($this->_filePath, $mode);

        if (false === $this->_fileHandler) {
            throw new \Magento\Exception('Failed to open file ' . $this->_filePath);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _write($data)
    {
        $result = @bzwrite($this->_fileHandler, $data);

        if (false === $result) {
            throw new \Magento\Exception('Failed to write data to ' . $this->_filePath);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _read($length)
    {
        $data = bzread($this->_fileHandler, $length);

        if (false === $data) {
            throw new \Magento\Exception('Failed to read data from ' . $this->_filePath);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function _close()
    {
        bzclose($this->_fileHandler);
    }
}

