<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_PostInc;

class PostIncrementOperator extends AbstractPostfixOperator
{
    /**
     * @param PHPParser_Node_Expr_PostInc $node
     */
    public function __construct(PHPParser_Node_Expr_PostInc $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '++';
    }

    /* 'Expr_PostInc'          => array( 1, -1), */
    /**
     * {@inheritdoc}
     */
    public function associativity()
    {
        return -1;
    }

    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 1;
    }
}
