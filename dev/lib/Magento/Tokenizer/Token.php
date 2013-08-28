<?php
/**
 * Token class
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Tokenizer_Token
{
    /**
     * @var int|string
     */
    protected $_value;

    /**
     * @var int|string
     */
    protected $_name = '';

    /**
     * @var int
     */
    protected $_line = 0;

    /**
     * Get line of token beginning
     *
     * @return int
     */
    public function getLine()
    {
        return $this->_line;
    }

    /**
     * Get token name
     *
     * @return int|string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get token value
     *
     * @return int|string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Build token
     *
     * @param array $tokenData
     */
    public function __construct($tokenData)
    {
        if (is_array($tokenData)) {
            $this->_name = $tokenData[0];
            $this->_value = $tokenData[1];
            $this->_line = $tokenData[2];
        } else {
            $this->_value = $this->_name = $tokenData;
        }
    }
}
