<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Expr_AssignBitwiseAnd;

class AssignBitwiseAndOperator extends AssignmentOperatorAbstract
{
    public function __construct(PHPParser_Node_Expr_AssignBitwiseAnd $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' &= ';
    }
    /* 'Expr_AssignBitwiseAnd' => array(15,  1), */
    public function associativity()
    {
        return 1;
    }
    public function precedence()
    {
        return 15;
    }
}
