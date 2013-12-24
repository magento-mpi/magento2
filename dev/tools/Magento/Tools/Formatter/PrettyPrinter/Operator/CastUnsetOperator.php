<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Unset;

class CastUnsetOperator extends AbstractCastOperator
{
    public function __construct(PHPParser_Node_Expr_Cast_Unset $node)
    {
        parent::__construct($node, 'unset');
    }
}
