<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Expr_Mul;

class MultiplyOperator extends InfixOperatorAbstract
{
    public function __construct(PHPParser_Node_Expr_Mul $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' * ';
    }
    /* 'Expr_Mul'              => array( 4, -1), */
    public function associativity()
    {
        return -1;
    }
    public function precedence()
    {
        return 4;
    }
}
