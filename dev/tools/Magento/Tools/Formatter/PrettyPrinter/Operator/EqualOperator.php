<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Equal;

class EqualOperator extends AbstractInfixOperator
{
    public function __construct(PHPParser_Node_Expr_Equal $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' == ';
    }
    /* 'Expr_Equal'            => array( 8,  0), */
    public function associativity()
    {
        0;
    }

    public function precedence()
    {
        return 8;
    }
}
