<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_ShiftLeft;

class ShiftLeftOperator extends AbstractLeftAssocOperator
{
    public function __construct(PHPParser_Node_Expr_ShiftLeft $node)
    {
        parent::__construct($node);
    }

    public function operator()
    {
        return '<<';
    }

    /* 'Expr_ShiftLeft'        => array( 6, -1), */
    public function precedence()
    {
        return 6;
    }
}
