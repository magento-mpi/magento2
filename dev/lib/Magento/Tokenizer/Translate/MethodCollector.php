<?php
/**
 * PPH phrase collector. Collect phrases from method __()
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Tokenizer_Translate_MethodCollector extends Magento_Tokenizer_PhraseCollector
{
    /**
     * Find phrases into given tokens. e.g.: __('phrase', ...)
     */
    protected function _findPhrases()
    {
        if ($this->_tokenizer->getNextToken()->getName() == T_OBJECT_OPERATOR) {
            $phraseStartToken = $this->_tokenizer->getNextToken();
            if (($this->_tokenizer->tokenIsEqualFunction($phraseStartToken, '__')
                || ($phraseStartToken->getName() == T_WHITESPACE
                    && $this->_tokenizer->tokenIsEqualFunction($this->_tokenizer->getNextToken(), '__')))
                && $this->_tokenizer->getNextToken()->getValue() == '('
            ) {
                $arguments = $this->_tokenizer->getFunctionArgumentsTokens();
                $phrase = $this->_collectPhrase(array_shift($arguments));
                $this->_addPhrase($phrase, count($arguments), $this->_file, $phraseStartToken->getLine());
            }
        }
    }
}