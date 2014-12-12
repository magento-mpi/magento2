<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_PostDec;

class PostDecrementOperator extends AbstractPostfixOperator
{
    /**
     * @param PHPParser_Node_Expr_PostDec $node
     */
    public function __construct(PHPParser_Node_Expr_PostDec $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '--';
    }

    /* 'Expr_PostDec'          => array( 1, -1), */
    /**
     * {@inheritdoc}
     */
    public function associativity()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 1;
    }
}
