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
     * Extract phrases from given tokens. e.g.: __('phrase', ...)
     */
    protected function _extractPhrases()
    {
        if ($this->_tokenizer->getNextToken()->isObjectOperator()) {
            $phraseStartToken = $this->_tokenizer->getNextToken();
            if ($this->_isTranslateFunction($phraseStartToken)) {
                $arguments = $this->_tokenizer->getFunctionArgumentsTokens();
                $phrase = $this->_collectPhrase(array_shift($arguments));
                $this->_addPhrase($phrase, count($arguments), $this->_file, $phraseStartToken->getLine());
            }
        }
    }

    /**
     * @param Magento_Tokenizer_Token $token
     * @return bool
     */
    protected function _isTranslateFunction($token)
    {
        return ($token->isEqualFunction('__') || ($token->isWhitespace()
                && $this->_tokenizer->getNextToken()->isEqualFunction('__')))
            && $this->_tokenizer->getNextToken()->isOpenBrace();
    }
}
