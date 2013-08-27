<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

/**
 * Dictionary generator
 */
class Generator implements GeneratorInterface
{
    /**
     * @param ParserInterface
     */
    private $_parser;

    /**
     * WriterInterface
     */
    private $_writer;

    /**
     * Dictionary generator construct
     *
     * @param ParserInterface $parser
     * @param WriterInterface $writer
     */
    public function __construct(ParserInterface $parser, WriterInterface $writer)
    {
        $this->_parser = $parser;
        $this->_writer = $writer;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($withContext = true)
    {
        $this->_parser->parse();

        foreach ($this->_parser->getPhrases() as $phrase) {
            $fields = array($phrase['phrase'], $phrase['phrase']);
            if ($withContext) {
                array_push($fields, $phrase['context_type'], implode(',', array_keys($phrase['context'])));
            }
            $this->_writer->write($fields);
        }
    }
}
