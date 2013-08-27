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
    private $_fileHandler;

    /**
     * {@inheritdoc}
     */
    public function __construct($outputFilename = null)
    {
        if ($outputFilename) {
            if (false === ($fileHandler = fopen($outputFilename, 'w'))) {
                throw new \InvalidArgumentException(sprintf('Cannot open file for write dictionary: "%s"',
                    $outputFilename));
            }
        } else {
            $fileHandler = STDOUT;
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
