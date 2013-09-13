<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

use Magento\Tools\I18n\Code\Factory;
use Magento\Tools\I18n\Code\ParserInterface;

/**
 * Dictionary generator
 */
class Generator
{
    /**
     * Parser
     *
     * @var \Magento\Tools\I18n\Code\ParserInterface
     */
    protected $_parser;

    /**
     * Contextual parser
     *
     * @var \Magento\Tools\I18n\Code\ParserInterface
     */
    protected $_contextualParser;

    /**
     * Domain abstract factory
     *
     * @var \Magento\Tools\I18n\Code\Factory
     */
    protected $_factory;

    /**
     * Generator construct
     *
     * @param \Magento\Tools\I18n\Code\ParserInterface $parser
     * @param \Magento\Tools\I18n\Code\ParserInterface $contextualParser
     * @param \Magento\Tools\I18n\Code\Factory $factory
     */
    public function __construct(ParserInterface $parser, ParserInterface $contextualParser, Factory $factory)
    {
        $this->_parser = $parser;
        $this->_contextualParser = $contextualParser;
        $this->_factory = $factory;
    }

    /**
     * Generate dictionary
     *
     * @param array $filesOptions
     * @param string $outputFilename
     * @param bool $withContext
     */
    public function generate(array $filesOptions, $outputFilename, $withContext = false)
    {
        $writer = $this->_factory->createDictionaryWriter($outputFilename);

        $parser = $this->_getActualParser($withContext);
        $parser->parse($filesOptions);

        foreach ($parser->getPhrases() as $phrase) {
            $writer->write($phrase);
        }
    }

    /**
     * Get actual parser
     *
     * @param bool $withContext
     * @return \Magento\Tools\I18n\Code\ParserInterface
     */
    protected function _getActualParser($withContext)
    {
        return $withContext ? $this->_contextualParser : $this->_parser;
    }
}
