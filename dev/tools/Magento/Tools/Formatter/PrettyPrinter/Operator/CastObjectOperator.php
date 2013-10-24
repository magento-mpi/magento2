<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Object;

class CastObjectOperator extends AbstractPrefixOperator
{
    public function __construct(PHPParser_Node_Expr_Cast_Object $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '(object) ';
    }
    /* 'Expr_Cast_Object'         => array( 1,  1), */
    public function associativity()
    {
        return 1;
    }

    public function precedence()
    {
        return 1;
    }
}
