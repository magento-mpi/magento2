<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignConcat;

class AssignConcatOperator extends AbstractAssignmentOperator
{
    /**
     * @param PHPParser_Node_Expr_AssignConcat $node
     */
    public function __construct(PHPParser_Node_Expr_AssignConcat $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return '.=';
    }
}
