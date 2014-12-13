<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignBitwiseOr;

class AssignBitwiseOrOperator extends AbstractAssignmentOperator
{
    /**
     * @param PHPParser_Node_Expr_AssignBitwiseOr $node
     */
    public function __construct(PHPParser_Node_Expr_AssignBitwiseOr $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '|=';
    }
}
