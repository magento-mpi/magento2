<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Smaller;

class SmallerOperator extends AbstractInfixOperator
{
    public function __construct(PHPParser_Node_Expr_Smaller $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '<';
    }
    /* 'Expr_Smaller'          => array( 7,  0), */
    public function associativity()
    {
        return 0;
    }

    public function precedence()
    {
        return 7;
    }
}
