<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Expr_AssignBitwiseOr;

class AssignBitwiseOrOperator extends AssignmentOperatorAbstract
{
    public function __construct(PHPParser_Node_Expr_AssignBitwiseOr $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' |= ';
    }
    /* 'Expr_AssignBitwiseOr'  => array(15,  1), */
    public function associativity()
    {
        return 1;
    }
    public function precedence()
    {
        return 15;
    }
}
