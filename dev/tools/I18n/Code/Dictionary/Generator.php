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
     * Domain abstract factory
     *
     * @var \Magento\Tools\I18n\Code\Factory
     */
    protected $_factory;

    /**
     * Generator construct
     *
     * @param \Magento\Tools\I18n\Code\ParserInterface $parser
     * @param \Magento\Tools\I18n\Code\Factory $factory
     */
    public function __construct(ParserInterface $parser, Factory $factory)
    {
        $this->_parser = $parser;
        $this->_factory = $factory;
    }

    /**
     * Generate dictionary
     *
     * @param array $parseOptions
     * @param string $outputFilename
     * @param bool $withContext
     */
    public function generate(array $parseOptions, $outputFilename, $withContext)
    {
        $writer = $this->_factory->createDictionaryWriter($outputFilename);

        $this->_parser->parse($parseOptions);
        foreach ($this->_parser->getPhrases() as $phrase) {
            $fields = array($phrase['phrase'], $phrase['phrase']);
            if ($withContext) {
                array_push($fields, $phrase['context_type'], implode(',', array_keys($phrase['context_values'])));
            }
            $writer->write($fields, $outputFilename);
        }
    }

    /**
     * Get result message
     *
     * @return string
     */
    public function getResultMessage()
    {
        return "\nDictionary successfully processed.\n";
    }
}
