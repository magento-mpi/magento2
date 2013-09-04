<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Parser;

use Magento\Tools\I18n\Code\ParserInterface;
use Magento\Tools\I18n\Code\Context;

/**
 * Abstract data parser
 */
abstract class AbstractParser implements ParserInterface
{
    /**
     * Files for parsing
     *
     * @var array
     */
    protected $_files;

    /**
     * Context
     *
     * @var \Magento\Tools\I18n\Code\Context
     */
    protected $_context;

    /**
     * Parsed phrases
     *
     * @var array
     */
    protected $_phrases = array();

    /**
     * Parser construct
     *
     * @param array $files
     * @param \Magento\Tools\I18n\Code\Context $context
     */
    public function __construct(array $files, Context $context)
    {
        $this->_files = $files;
        $this->_context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        $this->_phrases = array();

        foreach ($this->_files as $file) {
            $this->_parse($file);
        }
    }

    /**
     * Template method
     *
     * @param string $file
     */
    abstract protected function _parse($file);

    /**
     * {@inheritdoc}
     */
    public function getPhrases()
    {
        return $this->_phrases;
    }

    /**
     * Add phrase
     *
     * @param string $phrase
     * @param string $file
     * @param string|int $line
     * @throws \InvalidArgumentException
     */
    protected function _addPhrase($phrase, $file, $line = '')
    {
        if (!$phrase) {
            throw new \InvalidArgumentException(sprintf('Phrase cannot be empty. File: "%s" Line: "%s"', $file, $line));
        }
        $phrase = $this->_stripQuotes($phrase);
        list($contextType, $contextValue) = $this->_context->getContextByPath($file);
        $phraseKey = $contextType . '::' . $phrase;

        if (isset($this->_phrases[$phraseKey])) {
            $this->_phrases[$phraseKey]['context'][$contextValue] = 1;
        } else {
            $this->_phrases[$phraseKey] = array(
                'phrase' => $phrase,
                'file' => $file,
                'line' => $line,
                'context' => array($contextValue => 1),
                'context_type' => $contextType,
            );
        }
    }

    /**
     * Prepare phrase
     *
     * @param string $phrase
     * @return string
     */
    protected function _stripQuotes($phrase)
    {
        if ($this->_isFirstAndLastCharIsQuote($phrase)) {
            $phrase = substr($phrase, 1, strlen($phrase) - 2);
        }
        return $phrase;
    }

    /**
     * Check if first and last char is quote
     *
     * @param string $phrase
     * @return bool
     */
    protected function _isFirstAndLastCharIsQuote($phrase)
    {
        return ($phrase[0] == '"' || $phrase[0] == "'") && $phrase[0] == $phrase[strlen($phrase) - 1];
    }
}
