<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Identical;

class IdenticalOperator extends AbstractInfixOperator
{
    /**
     * @param PHPParser_Node_Expr_Identical $node
     */
    public function __construct(PHPParser_Node_Expr_Identical $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '===';
    }

    /* 'Expr_Identical'            => array( 8,  0), */
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
        return 8;
    }
}
