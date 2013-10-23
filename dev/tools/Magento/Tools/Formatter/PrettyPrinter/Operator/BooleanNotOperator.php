<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_BooleanNot;

class BooleanNotOperator extends AbstractPrefixOperator
{
    public function __construct(PHPParser_Node_Expr_BooleanNot $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '!';
    }
    /* 'Expr_BooleanNot'       => array( 3,  1), */
    public function associativity()
    {
        return 1;
    }
    public function precedence()
    {
        return 3;
    }
}
