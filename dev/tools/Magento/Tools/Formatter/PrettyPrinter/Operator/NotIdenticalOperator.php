<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_NotIdentical;

class NotIdenticalOperator extends AbstractInfixOperator
{
    public function __construct(PHPParser_Node_Expr_NotIdentical $node)
    {
        parent::__construct($node);
    }

    public function operator()
    {
        return '!==';
    }

    /* 'Expr_NotIdentical'         => array( 8,  0), */
    public function associativity()
    {
        return 0;
    }

    public function precedence()
    {
        return 8;
    }
}
