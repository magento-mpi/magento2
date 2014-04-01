<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignShiftRight;

class AssignShiftRightOperator extends AbstractAssignmentOperator
{
    /**
     * @param PHPParser_Node_Expr_AssignShiftRight $node
     */
    public function __construct(PHPParser_Node_Expr_AssignShiftRight $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '>>=';
    }
}
