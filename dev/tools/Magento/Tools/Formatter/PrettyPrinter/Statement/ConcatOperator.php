<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;


use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Concat;

class ConcatOperator extends InfixOperatorAbstract {
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Expr_Concat $node
     */
    public function __construct(PHPParser_Node_Expr_Concat $node)
    {
        parent::__construct($node);
    }

    public function operator()
    {
        return ' . ';
    }

    /* 'Expr_Concat'           => array( 5, -1), */
    public function associativity()
    {
        return -1;
    }

    public function precedence()
    {
        return 5;
    }
}
