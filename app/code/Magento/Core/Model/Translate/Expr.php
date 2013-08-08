<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Translate expression object
 */
class Magento_Core_Model_Translate_Expr
{
    /**
     * Text to translate
     *
     * @var string
     */
    protected $_text;

    /**
     * Module
     *
     * @var string
     */
    protected $_module;

    /**
     * Set string and module
     *
     * @param string $text
     * @param string $module
     */
    public function __construct($text = '', $module = '')
    {
        $this->_text    = $text;
        $this->_module  = $module;
    }

    /**
     * @param string $text
     * @return Magento_Core_Model_Translate_Expr
     */
    public function setText($text)
    {
        $this->_text = $text;
        return $this;
    }

    /**
     * Set expression module
     *
     * @param string $module
     * @return Magento_Core_Model_Translate_Expr
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     * Retrieve expression text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Retrieve expression module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Retrieve expression code
     *
     * @param   string $separator
     * @return  string
     */
    public function getCode($separator = Magento_Core_Model_Translate::SCOPE_SEPARATOR)
    {
        return $this->getModule() . $separator . $this->getText();
    }
}
