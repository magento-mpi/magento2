<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Expr_LogicalOr;

class LogicalOrOperator extends InfixOperatorAbstract
{
    public function __construct(PHPParser_Node_Expr_LogicalOr $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' or ';
    }
    /* 'Expr_LogicalOr'        => array(18, -1), */
    public function associativity()
    {
        return -1;
    }
    public function precedence()
    {
        return 18;
    }
}
