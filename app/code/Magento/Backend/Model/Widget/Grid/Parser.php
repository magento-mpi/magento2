<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Widget\Grid;

class Parser
{
    /**
     * List of allowed operations
     *
     * @var array
     */
    protected $_operations = array('-', '+', '/', '*');

    /**
     * Parse expression
     *
     * @param string $expression
     * @return array
     */
    public function parseExpression($expression)
    {
        $stack = array();
        $expression = trim($expression);
        foreach ($this->_operations as $operation) {
            $splittedExpr = preg_split('/\\' . $operation . '/', $expression, -1, PREG_SPLIT_DELIM_CAPTURE);
            if (count($splittedExpr) > 1) {
                for ($i=0; $i < count($splittedExpr); $i++) {
                    $stack = array_merge($stack, $this->parseExpression($splittedExpr[$i]));
                    if ($i > 0) {
                        $stack[] = $operation;
                    }
                }
                break;
            }
        }
        return empty($stack)? array($expression) : $stack;
    }

    /**
     * Check if string is operation
     *
     * @param $operation
     * @return bool
     */
    public function isOperation($operation)
    {
        return in_array($operation, $this->_operations);
    }
}
