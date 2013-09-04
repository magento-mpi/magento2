<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

use Magento\Tools\I18n\Code\Factory;

/**
 * Dictionary generator
 */
class Generator
{
    /**
     * Domain abstract factory
     *
     * @var \Magento\Tools\I18n\Code\Factory
     */
    protected $_factory;

    /**
     * Generator construct
     *
     * @param \Magento\Tools\I18n\Code\Factory $factory
     */
    public function __construct(Factory $factory)
    {
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
        $parser = $this->_factory->createParser($parseOptions);
        $writer = $this->_factory->createDictionaryWriter($outputFilename);

        $parser->parse();
        foreach ($parser->getPhrases() as $phrase) {
            $fields = array($phrase['phrase'], $phrase['phrase']);
            if ($withContext) {
                array_push($fields, $phrase['context_type'], implode(',', array_keys($phrase['context'])));
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
