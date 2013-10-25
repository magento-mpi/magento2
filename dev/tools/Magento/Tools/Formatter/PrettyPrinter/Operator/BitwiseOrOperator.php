<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_BitwiseOr;

class BitwiseOrOperator extends AbstractLeftAssocOperator
{
    public function __construct(PHPParser_Node_Expr_BitwiseOr $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '|';
    }
    /* 'Expr_BitwiseOr'        => array(11, -1), */
    public function precedence()
    {
        return 11;
    }
}
