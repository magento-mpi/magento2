<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Writer;

use Magento\Tools\I18n\Code\Dictionary\WriterInterface;
use \Magento\Tools\I18n\Code\Dictionary\Phrase;

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
    public function write(Phrase $phrase)
    {
        $fields = array($phrase->getPhrase(), $phrase->getTranslation());
        $encloseQuote = $phrase->getQuote() == Phrase::QUOTE_DOUBLE ? Phrase::QUOTE_DOUBLE : Phrase::QUOTE_SINGLE;
        $fields[0] = $this->_compileString($fields[0], $encloseQuote);
        $fields[1] = $this->_compileString($fields[1], $encloseQuote);
        if (($contextType = $phrase->getContextType()) && ($contextValue = $phrase->getContextValueAsString())) {
            $fields[] = $contextType;
            $fields[] = $contextValue;
        }

        fputcsv($this->_fileHandler, $fields, ',', '"');
    }

    /**
     * Compile PHP string based on quotes type it enclosed with
     *
     * @param string $string
     * @param string $encloseQuote
     * @return string
     */
    protected function _compileString($string, $encloseQuote)
    {
        $evalString = 'return ' . $encloseQuote . $string . $encloseQuote . ';';
        $result = @eval($evalString);
        return is_string($result) ? $result : $string;
    }

    /**
     * Close file handler
     */
    public function __destructor()
    {
        fclose($this->_fileHandler);
    }
}
