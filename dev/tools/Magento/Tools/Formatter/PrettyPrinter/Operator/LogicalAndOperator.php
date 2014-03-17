<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_LogicalAnd;

class LogicalAndOperator extends AbstractLeftAssocOperator
{
    /**
     * @param PHPParser_Node_Expr_LogicalAnd $node
     */
    public function __construct(PHPParser_Node_Expr_LogicalAnd $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return 'and';
    }

    /* 'Expr_LogicalAnd'       => array(16, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 16;
    }
}
