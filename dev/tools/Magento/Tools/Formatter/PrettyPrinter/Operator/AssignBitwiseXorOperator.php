<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignBitwiseXor;

class AssignBitwiseXorOperator extends AbstractAssignmentOperator
{
    /**
     * @param PHPParser_Node_Expr_AssignBitwiseXor $node
     */
    public function __construct(PHPParser_Node_Expr_AssignBitwiseXor $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '^=';
    }
}
