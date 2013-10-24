<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Identical;

class IdenticalOperator extends AbstractInfixOperator
{
    public function __construct(PHPParser_Node_Expr_Identical $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' === ';
    }
    /* 'Expr_Identical'            => array( 8,  0), */
    public function associativity()
    {
        0;
    }

    public function precedence()
    {
        return 8;
    }
}
