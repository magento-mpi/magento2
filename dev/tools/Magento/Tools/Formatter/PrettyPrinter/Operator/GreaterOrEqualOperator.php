<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_GreaterOrEqual;

class GreaterOrEqualOperator extends AbstractInfixOperator
{
    /**
     * @param PHPParser_Node_Expr_GreaterOrEqual $node
     */
    public function __construct(PHPParser_Node_Expr_GreaterOrEqual $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '>=';
    }

    /* 'Expr_GreaterOrEqual'   => array( 7,  0), */
    /**
     * {@inheritdoc}
     */
    public function associativity()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 7;
    }
}
