<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Expr_UnaryPlus;

class UnaryPlusOperator extends PrefixOperatorAbstract
{
    public function __construct(PHPParser_Node_Expr_UnaryPlus $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '+';
    }
    /* 'Expr_UnaryPlus'        => array( 1,  1), */
    public function associativity()
    {
        return 1;
    }
    public function precedence()
    {
        return 1;
    }
}
