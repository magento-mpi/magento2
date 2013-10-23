<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Expr_PreDec;

class PreDecrementOperator extends PrefixOperatorAbstract
{
    public function __construct(PHPParser_Node_Expr_PreDec $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '--';
    }
    /* 'Expr_PreDec'           => array( 1,  1), */
    public function associativity()
    {
        return 1;
    }
    public function precedence()
    {
        return 1;
    }
    public function expr()
    {
        return $this->node->var;
    }
}
