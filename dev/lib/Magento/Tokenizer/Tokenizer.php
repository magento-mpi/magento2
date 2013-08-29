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
    protected $_tokens = array();

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
        $content = file_get_contents($filePath);
        $this->_tokens = token_get_all($content);
    }

    /**
     * Whenever token is phrase function
     *
     * @param Magento_Tokenizer_Token $token
     * @param string $functionName
     * @return bool
     */
    public function tokenIsEqualFunction(Magento_Tokenizer_Token $token, $functionName)
    {
        return $token->getName() == T_STRING && $token->getValue() == $functionName;
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
                if ($token->getValue() == ';') {
                    break;
                }
                if ($token->getValue() == '(') {
                    $this->_skipInnerArgumentInvoke();
                    continue;
                }
                if ($token->getValue() == ')') {
                    $this->_closeBrackets++;
                }
                $arguments[$argumentN][] = $token;
                if ($token->getName() == ',' && $this->_isInnerArgumentClosed()) {
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
        while ($this->getNextToken()->getValue() != ')') {
            if ($this->getCurrentToken()->getValue() == ')') {
                $this->_closeBrackets++;
            }
            if ($this->getCurrentToken()->getValue() == '(') {
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
        $token = current($this->_tokens);
        return $this->_buildToken($token);
    }

    /**
     * Get next token
     *
     * @return Magento_Tokenizer_Token
     * @throws Exception
     */
    public function getNextToken()
    {
        $token = next($this->_tokens);
        if ($token) {
            return $this->_buildToken($token);
        }
        throw new Exception('Tokens is ended');
    }

    /**
     * Build token from array
     *
     * @param array $tokenData
     * @return Magento_Tokenizer_Token
     */
    private function _buildToken($tokenData)
    {
        return new Magento_Tokenizer_Token($tokenData);
    }
}
