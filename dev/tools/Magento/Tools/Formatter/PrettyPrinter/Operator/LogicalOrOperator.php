<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_LogicalOr;

class LogicalOrOperator extends AbstractLeftAssocOperator
{
    /**
     * @param PHPParser_Node_Expr_LogicalOr $node
     */
    public function __construct(PHPParser_Node_Expr_LogicalOr $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return 'or';
    }

    /* 'Expr_LogicalOr'        => array(18, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 18;
    }
}
