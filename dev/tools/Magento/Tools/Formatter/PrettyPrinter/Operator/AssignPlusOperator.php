<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_AssignPlus;

class AssignPlusOperator extends AbstractAssignmentOperator
{
    public function __construct(PHPParser_Node_Expr_AssignPlus $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '+=';
    }
    /* 'Expr_AssignPlus'       => array(15,  1), */
}
