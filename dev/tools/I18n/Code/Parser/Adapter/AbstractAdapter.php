<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Parser\Adapter;

use Magento\Tools\I18n\Code\Context;
use Magento\Tools\I18n\Code\Parser\AdapterInterface;

/**
 * Abstract parser adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
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
     * Adapter construct
     *
     * @param \Magento\Tools\I18n\Code\Context $context
     */
    public function __construct(Context $context)
    {
        $this->_context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($file)
    {
        $this->_phrases = array();
        $this->_parse($file);
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
            $this->_phrases[$phraseKey]['context_values'][$contextValue] = 1;
        } else {
            $this->_phrases[$phraseKey] = array(
                'phrase' => $phrase,
                'file' => $file,
                'line' => $line,
                'context_values' => array($contextValue => 1),
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
