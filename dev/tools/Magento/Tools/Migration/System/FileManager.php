<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Migration\System;

class FileManager
{
    /**
     * @var \Magento\Tools\Migration\System\FileReader
     */
    protected $_reader;

    /**
     * @var \Magento\Tools\Migration\System\WriterInterface
     */
    protected $_writer;


    /**
     * @param \Magento\Tools\Migration\System\FileReader $reader
     * @param \Magento\Tools\Migration\System\WriterInterface $writer
     */
    public function __construct(
        \Magento\Tools\Migration\System\FileReader $reader,
        \Magento\Tools\Migration\System\WriterInterface $writer
    ) {
        $this->_reader = $reader;
        $this->_writer = $writer;
    }

    /**
     * @param string $fileName
     * @param string $contents
     */
    public function write($fileName, $contents)
    {
        $this->_writer->write($fileName, $contents);
    }

    /**
     * Remove file
     *
     * @param $fileName
     */
    public function remove($fileName)
    {
        $this->_writer->remove($fileName);
    }

    /**
     * Retrieve contents of a file
     *
     * @param string $fileName
     * @return string
     */
    public function getContents($fileName)
    {
        return $this->_reader->getContents($fileName);
    }

    /**
     * Get file list
     *
     * @param string $pattern
     * @return array
     */
    public function getFileList($pattern)
    {
        return $this->_reader->getFileList($pattern);
    }
}
