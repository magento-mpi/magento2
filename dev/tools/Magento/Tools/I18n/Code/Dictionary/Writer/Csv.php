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
        $fields = $this->_filterFields($fields);
        if (($contextType = $phrase->getContextType()) && ($contextValue = $phrase->getContextValueAsString())) {
            $fields[] = $contextType;
            $fields[] = $contextValue;
        }

        fputcsv($this->_fileHandler, $fields, ',', '"');
    }

    /**
     * Filter phrase and its translation
     *
     * @param array $fields
     * @return array
     */
    protected function _filterFields(array $fields)
    {
        foreach ($fields as &$field) {
            $field = str_replace("\'", "'", $field);
        }
        return $fields;
    }

    /**
     * Close file handler
     */
    public function __destructor()
    {
        fclose($this->_fileHandler);
    }
}
