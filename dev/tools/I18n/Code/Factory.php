<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code;

use Magento\Tools\I18n\Code\Dictionary;
use Magento\Tools\I18n\Code\Parser;

/**
 *  Abstract Factory
 */
class Factory
{
    /**
     * Context
     *
     * @var \Magento\Tools\I18n\Code\Context
     */
    protected $_context;

    /**
     * Factory construct
     *
     * @param \Magento\Tools\I18n\Code\Parser\Factory $parserFactory
     */
    public function __construct(Parser\Factory $parserFactory)
    {
        $this->_parserFactory = $parserFactory;
    }

    /**
     * Create parser
     *
     * @param array $options
     * @return \Magento\Tools\I18n\Code\ParserInterface
     */
    public function createParser(array $options)
    {
        return $this->_parserFactory->createParser($options);
    }

    /**
     * Create dictionary writer
     *
     * @param string $filename
     * @return \Magento\Tools\I18n\Code\Dictionary\WriterInterface
     */
    public function createDictionaryWriter($filename = null)
    {
        switch (pathinfo($filename, \PATHINFO_EXTENSION)) {
            case 'csv':
                $writer = new Dictionary\Writer\Csv($filename);
                break;
            default:
                $writer = new Dictionary\Writer\Csv\Stdo();
        }
        return $writer;
    }

    /**
     * Create locale
     *
     * @param string $locale
     * @return \Magento\Tools\I18n\Code\Locale
     */
    public static function createLocale($locale)
    {
        return new Locale($locale);
    }

    /**
     * Create dictionary
     *
     * @return \Magento\Tools\I18n\Code\Dictionary
     */
    public function createDictionary()
    {
        return new Dictionary();
    }

    /**
     * Create Phrase
     *
     * Row format:
     * 0 cell: phrase
     * 1: translate
     * 2: context type
     * 3: context value
     * 4: line
     *
     * @param array $data
     * @return \Magento\Tools\I18n\Code\Dictionary\Phrase
     */
    public function createPhrase(array $data)
    {
        return new Dictionary\Phrase(
            $data['phrase'],
            $data['translation'],
            $data['contextType'],
            $data['contextValue'],
            $data['line']
        );
    }
}
