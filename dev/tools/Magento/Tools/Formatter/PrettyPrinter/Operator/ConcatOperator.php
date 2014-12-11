<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Concat;

class ConcatOperator extends AbstractLeftAssocOperator
{
    /**
     * This method constructs a new statement based on the specified expression.
     * @param PHPParser_Node_Expr_Concat $node
     */
    public function __construct(PHPParser_Node_Expr_Concat $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '.';
    }

    /* 'Expr_Concat'           => array( 5, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 5;
    }
}
