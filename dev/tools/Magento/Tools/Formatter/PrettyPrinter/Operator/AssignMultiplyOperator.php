<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignMul;

class AssignMultiplyOperator extends AbstractAssignmentOperator
{
    public function __construct(PHPParser_Node_Expr_AssignMul $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' *= ';
    }
    /* 'Expr_AssignMul'        => array(15,  1), */
    public function associativity()
    {
        return 1;
    }
    public function precedence()
    {
        return 15;
    }
}
