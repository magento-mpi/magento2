<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignBitwiseAnd;

class AssignBitwiseAndOperator extends AbstractAssignmentOperator
{
    /**
     * @param PHPParser_Node_Expr_AssignBitwiseAnd $node
     */
    public function __construct(PHPParser_Node_Expr_AssignBitwiseAnd $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '&=';
    }
}
