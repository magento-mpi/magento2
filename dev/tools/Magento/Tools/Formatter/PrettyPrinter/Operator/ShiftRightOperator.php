<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_ShiftRight;

class ShiftRightOperator extends AbstractLeftAssocOperator
{
    /**
     * @param PHPParser_Node_Expr_ShiftRight $node
     */
    public function __construct(PHPParser_Node_Expr_ShiftRight $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '>>';
    }

    /* 'Expr_ShiftRight'        => array( 6, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 6;
    }
}
