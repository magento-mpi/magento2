<?php

class Mage_Backend_Model_Widget_Grid_Parser
{
    protected $_operations = array('+', '-', '*', '/');

    public function parseExpression($expression)
    {
        $stack = array();

        foreach ($this->_operations as $operation) {
            if (strpos($expression, $operation) !== false) {
                list($operand1, $operand2) = explode($operation, $expression);
                array_push($stack, $operand1);
                array_push($stack, $operand2);
                array_push($stack, $operation);
                break;
            }
        }
        return $stack;
    }
}
