<?php
/**
 * Filter for removing malicious code from HTML
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filter\Input;

class MaliciousCode implements \Zend_Filter_Interface
{
    /**
     * Regular expressions for cutting malicious code
     *
     * @var string[]
     */
    protected $_expressions = array(
        '/(\/\*.*\*\/)/Us',
        '/(\t)/',
        '/(javascript\s*:)/Usi',
        '/(@import)/Usi',
        '/style=[^<]*((expression\s*?\([^<]*?\))|(behavior\s*:))[^<]*(?=\>)/Uis',
        '/(ondblclick|onclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onload|onunload|onerror)=[^<]*(?=\>)/Uis',
        '/<\/?(script|meta|link|frame|iframe).*>/Uis',
        '/src=[^<]*base64[^<]*(?=\>)/Uis'
    );

    /**
     * Filter value
     *
     * @param string|array $value
     * @return string|array Filtered value
     */
    public function filter($value)
    {
        return preg_replace($this->_expressions, '', $value);
    }

    /**
     * Add expression
     *
     * @param string $expression
     * @return $this
     */
    public function addExpression($expression)
    {
        if (!in_array($expression, $this->_expressions)) {
            $this->_expressions[] = $expression;
        }
        return $this;
    }

    /**
     * Set expressions
     *
     * @param array $expressions
     * @return $this
     */
    public function setExpressions(array $expressions)
    {
        $this->_expressions = $expressions;
        return $this;
    }
}
