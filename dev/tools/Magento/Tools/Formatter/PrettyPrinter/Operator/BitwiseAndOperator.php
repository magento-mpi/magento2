<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_BitwiseAnd;

class BitwiseAndOperator extends AbstractLeftAssocOperator
{
    /**
     * @param PHPParser_Node_Expr_BitwiseAnd $node
     */
    public function __construct(PHPParser_Node_Expr_BitwiseAnd $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '&';
    }

    /* 'Expr_BitwiseAnd'       => array( 9, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 9;
    }
}
