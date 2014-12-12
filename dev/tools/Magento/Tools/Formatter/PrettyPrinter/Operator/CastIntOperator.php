<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Int;

class CastIntOperator extends AbstractCastOperator
{
    /**
     * @param PHPParser_Node_Expr_Cast_Int $node
     */
    public function __construct(PHPParser_Node_Expr_Cast_Int $node)
    {
        parent::__construct($node, 'int');
    }
}
