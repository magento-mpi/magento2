<?php
/**
 * PPH phrase collector. Collect phrases from __() function
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Tokenizer_PhraseCollector
{
    /**
     * @var Magento_Tokenizer_Tokenizer
     */
    protected $_tokenizer;

    /**
     * @var array
     */
    protected $_phrases = array();

    /**
     * @var SplFileInfo
     */
    protected $_file;

    /**
     * Contruct
     */
    public function __construct()
    {
        $this->_tokenizer = new Magento_Tokenizer_Tokenizer();
    }

    /**
     * Get phrases for given file
     *
     * @return array
     */
    public function getPhrases()
    {
        return $this->_phrases;
    }

    /**
     * Parse given files for phrase
     *
     * @param string $file
     */
    public function parse($file)
    {
        $this->_phrases = array();
        $this->_file = $file;
        $this->_tokenizer->parse($file);
        try {
            for (; ;) {
                $this->findPhrases();
            }
        } catch (Exception $pe) {
            // tokens is ended in file
        }
    }

    /**
     * Find phrases into given tokens. e.g.: __('phrase', ...)
     */
    protected function findPhrases()
    {
        $phraseStartToken = $this->_tokenizer->getNextToken();
        if ($this->_tokenizer->tokenIsEqualFunction($phraseStartToken, '__')
            && $this->_tokenizer->getNextToken()->getValue() == '('
        ) {
            $arguments = $this->_tokenizer->getFunctionArgumentsTokens();
            $phrase = $this->collectPhrase(array_shift($arguments));
            if (null !== $phrase) {
                $this->addPhrase($phrase, count($arguments), $this->_file, $phraseStartToken->getLine());
            }
        }
    }

    /**
     * Collect all phrase parts into string. Return null if phrase is a variable
     *
     * @param array $phraseTokens
     * @return string|null
     */
    protected function collectPhrase($phraseTokens)
    {
        $phrase = array();
        if ($phraseTokens) {
            $isNotLiteral = true;
            /** @var $phraseToken Magento_Tokenizer_Token*/
            foreach ($phraseTokens as $phraseToken) {
                if ($phraseToken->getName() == T_CONSTANT_ENCAPSED_STRING) {
                    $phrase[] = $phraseToken->getValue();
                    $isNotLiteral = false;
                }
            }
            if ($isNotLiteral) {
                return null;
            }
        }
        return implode(' ', $phrase);
    }

    /**
     * Add phrase
     *
     * @param string $phrase
     * @param int $argumentsAmount
     * @param SplFileInfo $file
     * @param int $line
     */
    protected function addPhrase($phrase, $argumentsAmount, $file, $line)
    {
        $this->_phrases[] = array(
            'phrase' => $phrase,
            'arguments' => $argumentsAmount,
            'file' => $file,
            'line' => $line,
        );
    }
}
