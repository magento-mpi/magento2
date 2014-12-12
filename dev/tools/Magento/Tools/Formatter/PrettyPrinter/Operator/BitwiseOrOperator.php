<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_BitwiseOr;

class BitwiseOrOperator extends AbstractLeftAssocOperator
{
    /**
     * @param PHPParser_Node_Expr_BitwiseOr $node
     */
    public function __construct(PHPParser_Node_Expr_BitwiseOr $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '|';
    }

    /* 'Expr_BitwiseOr'        => array(11, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 11;
    }
}
