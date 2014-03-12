<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignDiv;

class AssignDivideOperator extends AbstractAssignmentOperator
{
    /**
     * @param PHPParser_Node_Expr_AssignDiv $node
     */
    public function __construct(PHPParser_Node_Expr_AssignDiv $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '/=';
    }
}
