<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Int;

class CastIntOperator extends AbstractCastOperator
{
    public function __construct(PHPParser_Node_Expr_Cast_Int $node)
    {
        parent::__construct($node, 'int');
    }
}
