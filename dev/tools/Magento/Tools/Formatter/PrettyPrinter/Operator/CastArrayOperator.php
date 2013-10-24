<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Array;

class CastArrayOperator extends AbstractPrefixOperator
{
    public function __construct(PHPParser_Node_Expr_Cast_Array $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '(array) ';
    }
    /* 'Expr_Cast_Array'         => array( 1,  1), */
    public function associativity()
    {
        return 1;
    }

    public function precedence()
    {
        return 1;
    }
}
