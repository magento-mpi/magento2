<?php
/**
 * Split php into tokens and provide methods for iterate through tokens
 * note: implementation of Iterator is to slow for this purpose
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Tokenizer_Tokenizer
{
    /**
     * @var array
     */
    private $_tokens = array();

    /**
     * @var int
     */
    private $_tokensCount;

    /**
     * @var int
     */
    private $_openBrackets;

    /**
     * @var int
     */
    private $_closeBrackets;

    /**
     * Parse given file
     *
     * @param string $filePath
     */
    public function parse($filePath)
    {
        $this->_tokens = token_get_all(file_get_contents($filePath));
        $this->_tokensCount = count($this->_tokens);
    }

    /**
     * Get arguments tokens of function
     *
     * @return array
     */
    public function getFunctionArgumentsTokens()
    {
        $arguments = array();
        try {
            $this->_openBrackets = 1;
            $this->_closeBrackets = 0;
            $argumentN = 0;
            while (true) {
                $token = $this->getNextToken();
                if ($token->isSemicolon()) {
                    break;
                }
                if ($token->isOpenBrace()) {
                    $this->_skipInnerArgumentInvoke();
                    continue;
                }
                if ($token->isCloseBrace()) {
                    $this->_closeBrackets++;
                }
                $arguments[$argumentN][] = $token;
                if ($token->isComma() && $this->_isInnerArgumentClosed()) {
                    array_pop($arguments[$argumentN]);
                    $argumentN++;
                }
                if ($this->_openBrackets == $this->_closeBrackets) {
                    break;
                }
            }
        } catch (Exception $e) {
            return array();
        }
        return $arguments;
    }

    /**
     * Whenever inner argument closed
     *
     * @return bool
     */
    private function _isInnerArgumentClosed()
    {
        return ($this->_openBrackets - 1) == $this->_closeBrackets;
    }

    /**
     * Skip invoke the inner argument of function
     */
    private function _skipInnerArgumentInvoke()
    {
        $this->_openBrackets++;
        while (!$this->getNextToken()->isCloseBrace()) {
            if ($this->getCurrentToken()->isCloseBrace()) {
                $this->_closeBrackets++;
            }
            if ($this->getCurrentToken()->isOpenBrace()) {
                $this->_openBrackets++;
            }
        }
        $this->_closeBrackets++;
    }

    /**
     * Get current token
     *
     * @return Magento_Tokenizer_Token
     */
    public function getCurrentToken()
    {
        return $this->_createToken(current($this->_tokens));
    }

    /**
     * Get next token
     *
     * @return bool|Magento_Tokenizer_Token
     */
    public function getNextToken()
    {
        return ($token = next($this->_tokens)) ? $this->_createToken($token) : false;
    }

    /**
     * Check is it last token
     */
    public function isLastToken()
    {
        return (key($this->_tokens) + 1) == $this->_tokensCount;
    }

    /**
     * Create token from array|string
     *
     * @param array|string $tokenData
     * @return Magento_Tokenizer_Token
     */
    private function _createToken($tokenData)
    {
        if (is_array($tokenData)) {
            return new Magento_Tokenizer_Token($tokenData[0], $tokenData[1], $tokenData[2]);
        } else {
            return new Magento_Tokenizer_Token($tokenData, $tokenData);
        }
    }
}
