<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_PreInc;

class PreIncrementOperator extends AbstractPrefixOperator
{
    public function __construct(PHPParser_Node_Expr_PreInc $node)
    {
        parent::__construct($node);
    }

    public function operator()
    {
        return '++';
    }

    /* 'Expr_PreInc'           => array( 1,  1), */
    public function associativity()
    {
        return 1;
    }

    public function precedence()
    {
        return 1;
    }

    public function expr()
    {
        return $this->node->var;
    }
}
