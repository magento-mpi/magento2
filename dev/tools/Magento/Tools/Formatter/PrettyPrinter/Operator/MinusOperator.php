<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Minus;

class MinusOperator extends AbstractMathOperator
{
    public function __construct(PHPParser_Node_Expr_Minus $node)
    {
        parent::__construct($node);
    }

    public function operator()
    {
        return '-';
    }

    /* 'Expr_Minus'            => array( 5, -1), */
    public function precedence()
    {
        return 5;
    }
}
