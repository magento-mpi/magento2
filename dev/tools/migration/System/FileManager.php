<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_System_FileManager
{
    /**
     * @var Tools_Migration_System_FileReader
     */
    protected $_reader;

    /**
     * @var Tools_Migration_System_WriterInterface
     */
    protected $_writer;


    /**
     * @param Tools_Migration_System_FileReader $reader
     * @param Tools_Migration_System_WriterInterface $writer
     */
    public function __construct(
        Tools_Migration_System_FileReader $reader,
        Tools_Migration_System_WriterInterface $writer
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
