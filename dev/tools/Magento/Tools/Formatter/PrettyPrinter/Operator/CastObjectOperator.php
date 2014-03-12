<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Object;

class CastObjectOperator extends AbstractCastOperator
{
    /**
     * @param PHPParser_Node_Expr_Cast_Object $node
     */
    public function __construct(PHPParser_Node_Expr_Cast_Object $node)
    {
        parent::__construct($node, 'object');
    }
}
