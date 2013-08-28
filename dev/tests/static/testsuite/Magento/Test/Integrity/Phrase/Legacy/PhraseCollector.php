<?php
/**
 * PPH phrase collector. Collect phrases from method __()
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Test_Integrity_Phrase_Legacy_PhraseCollector extends Magento_Tokenizer_PhraseCollector
{
    /**
     * Find phrases into given tokens. e.g.: __('phrase', ...)
     */
    protected function findPhrases()
    {
        if ($this->_tokenizer->getNextToken()->getName() == T_OBJECT_OPERATOR) {
            $phraseStartToken = $this->_tokenizer->getNextToken();
            if (($this->_tokenizer->tokenIsEqualFunction($phraseStartToken, '__')
                || ($phraseStartToken->getName() == T_WHITESPACE
                    && $this->_tokenizer->tokenIsEqualFunction($this->_tokenizer->getNextToken(), '__')))
                && $this->_tokenizer->getNextToken()->getValue() == '('
            ) {
                $arguments = $this->_tokenizer->getFunctionArgumentsTokens();
                $phrase = $this->collectPhrase(array_shift($arguments));
                $this->addPhrase($phrase, count($arguments), $this->_file, $phraseStartToken->getLine());
            }
        }
    }
}
