<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Parser;

use Magento\Tools\I18n\Code\Dictionary\ParserInterface;

/**
 * Composite data parser
 */
class Composite implements ParserInterface
{
    /**
     * List of ParserInterface
     *
     * @var array|ParserInterface[]
     */
    protected $_parsers = array();

    /**
     * Add parser
     *
     * @param ParserInterface $parser
     */
    public function add(ParserInterface $parser)
    {
        $this->_parsers[] = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        foreach ($this->_parsers as $parser) {
            $parser->parse();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPhrases()
    {
        $phrases = array();
        foreach ($this->_parsers as $parser) {
            $phrases = array_merge($phrases, $parser->getPhrases());
        }
        return $phrases;
    }
}
