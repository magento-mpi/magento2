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
 * CSV import adapter
 */
namespace Magento\ImportExport\Model\Import\Source;

class Csv extends \Magento\ImportExport\Model\Import\SourceAbstract
{
    /**
     * @var resource
     */
    protected $_file;

    /**
     * @var string
     */
    protected $_delimiter = '';

    /**
     * @var string
     */
    protected $_enclosure = '';

    /**
     * Open file and detect column names
     *
     * There must be column names in the first line
     *
     * @param string $fileOrStream
     * @param string $delimiter
     * @param string $enclosure
     * @throws \LogicException
     */
    public function __construct($fileOrStream, $delimiter = ',', $enclosure = '"')
    {
        $this->_file = @fopen($fileOrStream, 'r');
        if (false === $this->_file) {
            throw new \LogicException("Unable to open file or stream: '{$fileOrStream}'");
        }
        $this->_delimiter = $delimiter;
        $this->_enclosure = $enclosure;
        parent::__construct($this->_getNextRow());
    }

    /**
     * Close file handle
     */
    public function __destruct()
    {
        if (is_resource($this->_file)) {
            fclose($this->_file);
        }
    }

    /**
     * Read next line from CSV-file
     *
     * @return array|bool
     */
    protected function _getNextRow()
    {
        return fgetcsv($this->_file, null, $this->_delimiter, $this->_enclosure);
    }

    /**
     * Rewind the \Iterator to the first element (\Iterator interface)
     */
    public function rewind()
    {
        rewind($this->_file);
        $this->_getNextRow(); // skip first line with the header
        parent::rewind();
    }
}
