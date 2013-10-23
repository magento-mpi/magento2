<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_PostInc;

class PostIncrementOperator extends AbstractPostfixOperator
{
    public function __construct(PHPParser_Node_Expr_PostInc $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '++';
    }
    /* 'Expr_PostInc'          => array( 1, -1), */
    public function associativity()
    {
        return -1;
    }
    public function precedence()
    {
        return 1;
    }
}
