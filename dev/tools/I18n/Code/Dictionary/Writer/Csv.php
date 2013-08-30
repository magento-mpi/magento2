<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Writer;

use Magento\Tools\I18n\Code\Dictionary\WriterInterface;

/**
 * Csv writer
 */
class Csv implements WriterInterface
{
    /**
     * File handler
     *
     * @var resource
     */
    protected $_fileHandler;

    /**
     * Writer construct
     *
     * @param string $outputFilename
     * @throws \InvalidArgumentException
     */
    public function __construct($outputFilename)
    {
        if (false === ($fileHandler = @fopen($outputFilename, 'w'))) {
            throw new \InvalidArgumentException(sprintf('Cannot open file for write dictionary: "%s"',
                $outputFilename));
        }
        $this->_fileHandler = $fileHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $fields)
    {
        fputcsv($this->_fileHandler, $fields, ',', '"');
    }

    /**
     * Close file handler
     */
    public function __destructor()
    {
        fclose($this->_fileHandler);
    }
}
