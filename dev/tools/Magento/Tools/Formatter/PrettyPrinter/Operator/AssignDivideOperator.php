<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignDiv;

class AssignDivideOperator extends AbstractAssignmentOperator
{
    public function __construct(PHPParser_Node_Expr_AssignDiv $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' /= ';
    }
    /* 'Expr_AssignDiv'        => array(15,  1), */
    public function associativity()
    {
        return 1;
    }
    public function precedence()
    {
        return 15;
    }
}
