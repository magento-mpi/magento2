<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_PreDec;

class PreDecrementOperator extends AbstractPrefixOperator
{
    /**
     * @param PHPParser_Node_Expr_PreDec $node
     */
    public function __construct(PHPParser_Node_Expr_PreDec $node)
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

    /* 'Expr_PreDec'           => array( 1,  1), */
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

    /**
     * {@inheritdoc}
     */
    public function expr()
    {
        return $this->node->var;
    }
}
