<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_BitwiseAnd;

class BitwiseAndOperator extends AbstractLeftAssocOperator
{
    public function __construct(PHPParser_Node_Expr_BitwiseAnd $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '&';
    }
    /* 'Expr_BitwiseAnd'       => array( 9, -1), */
    public function precedence()
    {
        return 9;
    }
}
