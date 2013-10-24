<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_BitwiseXor;

class BitwiseXorOperator extends AbstractInfixOperator
{
    public function __construct(PHPParser_Node_Expr_BitwiseXor $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' ^ ';
    }
    /* 'Expr_BitwiseXor'       => array(10, -1), */
    public function associativity()
    {
        return -1;
    }
    public function precedence()
    {
        return 10;
    }
}
