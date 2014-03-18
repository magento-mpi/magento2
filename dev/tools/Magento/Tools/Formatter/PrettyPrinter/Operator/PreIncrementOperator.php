<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_PreInc;

class PreIncrementOperator extends AbstractPrefixOperator
{
    /**
     * @param PHPParser_Node_Expr_PreInc $node
     */
    public function __construct(PHPParser_Node_Expr_PreInc $node)
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

    /* 'Expr_PreInc'           => array( 1,  1), */
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
