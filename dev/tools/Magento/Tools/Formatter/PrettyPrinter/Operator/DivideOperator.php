<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Div;

class DivideOperator extends AbstractMathOperator
{
    public function __construct(PHPParser_Node_Expr_Div $node)
    {
        parent::__construct($node);
    }

    public function operator()
    {
        return '/';
    }
}
