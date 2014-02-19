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
use Magento\Tools\I18n\Code\Dictionary\Phrase;

/**
 * Abstract parser adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Processed file
     *
     * @var string
     */
    protected $_file;

    /**
     * Parsed phrases
     *
     * @var array
     */
    protected $_phrases = array();

    /**
     * {@inheritdoc}
     */
    public function parse($file)
    {
        $this->_phrases = array();
        $this->_file = $file;
        $this->_parse();
    }

    /**
     * Template method
     */
    abstract protected function _parse();

    /**
     * {@inheritdoc}
     */
    public function getPhrases()
    {
        return array_values($this->_phrases);
    }

    /**
     * Add phrase
     *
     * @param string $phrase
     * @param string|int $line
     * @throws \InvalidArgumentException
     */
    protected function _addPhrase($phrase, $line = '')
    {
        if (!$phrase) {
            throw new \InvalidArgumentException(sprintf('Phrase cannot be empty. File: "%s" Line: "%s"',
                $this->_file, $line));
        }
        if (!isset($this->_phrases[$phrase])) {
            $quote = '';
            if ($this->_isFirstAndLastCharIsQuote($phrase)) {
                $quote = $phrase[0];
                $phrase = $this->_stripFirstAndLastChar($phrase);
            }

            $this->_phrases[$phrase] = array(
                'phrase' => $phrase,
                'file' => $this->_file,
                'line' => $line,
                'quote' => $quote
            );
        }
    }

    /**
     * Prepare phrase
     *
     * @param string $phrase
     * @return string
     */
    protected function _stripFirstAndLastChar($phrase)
    {
        return substr($phrase, 1, strlen($phrase) - 2);
    }

    /**
     * Check if first and last char is quote
     *
     * @param string $phrase
     * @return bool
     */
    protected function _isFirstAndLastCharIsQuote($phrase)
    {
        return ($phrase[0] == Phrase::QUOTE_DOUBLE || $phrase[0] == Phrase::QUOTE_SINGLE)
            && $phrase[0] == $phrase[strlen($phrase) - 1];
    }
}
