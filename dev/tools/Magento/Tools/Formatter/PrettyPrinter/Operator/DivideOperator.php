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
    /**
     * @param PHPParser_Node_Expr_Div $node
     */
    public function __construct(PHPParser_Node_Expr_Div $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '/';
    }
}
