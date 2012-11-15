<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Widget_Grid_Parser
{
    /**
     * List of allowed operations
     *
     * @var array
     */
    protected $_operations = array('+', '-', '*', '/');

    /**
     * Parse expression
     *
     * @param $expression
     * @return array
     */
    public function parseExpression($expression)
    {
        $stack = array();

        foreach ($this->_operations as $operation) {
            if (strpos($expression, $operation) !== false) {
                list($operand1, $operand2) = explode($operation, $expression);
                $stack = array($operand1, $operand2, $operation);
                break;
            }
        }
        return $stack;
    }
}
