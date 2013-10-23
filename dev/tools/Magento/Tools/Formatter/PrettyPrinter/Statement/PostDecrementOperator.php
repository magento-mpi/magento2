<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Expr_PostDec;

class PostDecrementOperator extends PostfixOperatorAbstract
{
    public function __construct(PHPParser_Node_Expr_PostDec $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '--';
    }
    /* 'Expr_PostDec'          => array( 1, -1), */
    public function associativity()
    {
        return 1;
    }
    public function precedence()
    {
        return 1;
    }
}
