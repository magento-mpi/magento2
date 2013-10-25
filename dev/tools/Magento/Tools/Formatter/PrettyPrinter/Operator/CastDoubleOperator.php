<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Double;

class CastDoubleOperator extends AbstractCastOperator
{
    public function __construct(PHPParser_Node_Expr_Cast_Double $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '(double) ';
    }
    /* 'Expr_Cast_Double'      => array( 1,  1), */
}
