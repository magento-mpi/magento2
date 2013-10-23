<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Div;

class DivideOperator extends AbstractInfixOperator
{
    public function __construct(PHPParser_Node_Expr_Div $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' / ';
    }
    /* 'Expr_Div'              => array( 4, -1), */
    public function associativity()
    {
        return -1;
    }
    public function precedence()
    {
        return 4;
    }
}
