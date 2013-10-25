<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignShiftLeft;

class AssignShiftLeftOperator extends AbstractAssignmentOperator
{
    public function __construct(PHPParser_Node_Expr_AssignShiftLeft $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '<<=';
    }
    /* 'Expr_AssignShiftLeft'  => array(15,  1), */
}
