<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Greater;

class GreaterOperator extends AbstractInfixOperator
{
    /**
     * @param PHPParser_Node_Expr_Greater $node
     */
    public function __construct(PHPParser_Node_Expr_Greater $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '>';
    }

    /* 'Expr_Greater'          => array( 7,  0), */
    /**
     * {@inheritdoc}
     */
    public function associativity()
    {
        0;
    }

    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 7;
    }
}
