<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Expr_AssignPlus;

class AssignPlusOperator extends AssignmentOperatorAbstract
{
    public function __construct(PHPParser_Node_Expr_AssignPlus $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return ' += ';
    }
    /* 'Expr_AssignPlus'       => array(15,  1), */
    public function associativity()
    {
        return 1;
    }
    public function precedence()
    {
        return 15;
    }
}
