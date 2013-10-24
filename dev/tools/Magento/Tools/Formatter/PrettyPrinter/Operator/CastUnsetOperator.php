<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Unset;

class CastUnsetOperator extends AbstractPrefixOperator
{
    public function __construct(PHPParser_Node_Expr_Cast_Unset $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '(unset) ';
    }
    /* 'Expr_Cast_Unset'       => array( 1,  1), */
    public function associativity()
    {
        return 1;
    }

    public function precedence()
    {
        return 1;
    }
}
