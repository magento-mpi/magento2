<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Plus;

class PlusOperator extends AbstractMathOperator
{
    /**
     * @param PHPParser_Node_Expr_Plus $node
     */
    public function __construct(PHPParser_Node_Expr_Plus $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '+';
    }

    /* 'Expr_Plus'             => array( 5, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 5;
    }
}
