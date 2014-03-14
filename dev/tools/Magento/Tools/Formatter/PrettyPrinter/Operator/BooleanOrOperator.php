<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_BooleanOr;

class BooleanOrOperator extends AbstractLeftAssocOperator
{
    /**
     * @param PHPParser_Node_Expr_BooleanOr $node
     */
    public function __construct(PHPParser_Node_Expr_BooleanOr $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '||';
    }

    /* 'Expr_BooleanOr'        => array(13, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 13;
    }
}
