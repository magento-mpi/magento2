<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Minus;

class MinusOperator extends AbstractMathOperator
{
    /**
     * @param PHPParser_Node_Expr_Minus $node
     */
    public function __construct(PHPParser_Node_Expr_Minus $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '-';
    }

    /* 'Expr_Minus'            => array( 5, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 5;
    }
}
