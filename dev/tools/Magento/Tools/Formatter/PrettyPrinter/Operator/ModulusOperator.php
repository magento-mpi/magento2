<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Mod;

class ModulusOperator extends AbstractMathOperator
{
    public function __construct(PHPParser_Node_Expr_Mod $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '%';
    }
    /* 'Expr_Mod'              => array( 4, -1), */
}
