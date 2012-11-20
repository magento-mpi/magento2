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
                list($firstOperand, $secondOperand) = explode($operation, $expression);
                $stack = array($firstOperand, $secondOperand, $operation);
                break;
            }
        }
        return $stack;
    }
}
