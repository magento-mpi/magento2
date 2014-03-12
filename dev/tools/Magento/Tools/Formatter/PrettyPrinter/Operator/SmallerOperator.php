<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Smaller;

class SmallerOperator extends AbstractInfixOperator
{
    /**
     * @param PHPParser_Node_Expr_Smaller $node
     */
    public function __construct(PHPParser_Node_Expr_Smaller $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '<';
    }

    /* 'Expr_Smaller'          => array( 7,  0), */
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
