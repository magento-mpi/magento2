<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Unset;

class CastUnsetOperator extends AbstractCastOperator
{
    /**
     * @param PHPParser_Node_Expr_Cast_Unset $node
     */
    public function __construct(PHPParser_Node_Expr_Cast_Unset $node)
    {
        parent::__construct($node, 'unset');
    }
}
