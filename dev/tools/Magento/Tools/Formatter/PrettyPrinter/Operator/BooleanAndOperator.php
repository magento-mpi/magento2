<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_BooleanAnd;

class BooleanAndOperator extends AbstractLeftAssocOperator
{
    /**
     * @param PHPParser_Node_Expr_BooleanAnd $node
     */
    public function __construct(PHPParser_Node_Expr_BooleanAnd $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '&&';
    }

    /* 'Expr_BooleanAnd'       => array(12, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 12;
    }
}
