<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_BitwiseXor;

class BitwiseXorOperator extends AbstractLeftAssocOperator
{
    /**
     * @param PHPParser_Node_Expr_BitwiseXor $node
     */
    public function __construct(PHPParser_Node_Expr_BitwiseXor $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '^';
    }

    /* 'Expr_BitwiseXor'       => array(10, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 10;
    }
}
