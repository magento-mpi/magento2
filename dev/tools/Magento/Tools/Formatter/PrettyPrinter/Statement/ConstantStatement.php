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
use Magento\Tools\Formatter\PrettyPrinter\SimpleListLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_ClassConst;

class ConstantStatement extends ClassMemberAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Stmt_ClassConst $node
     */
    public function __construct(PHPParser_Node_Stmt_ClassConst $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the const line
        $line->add('const ');
        // add in the list of actual constants
        $this->processArgumentList($this->node->consts, $treeNode, $line, new SimpleListLineBreak());
        // terminate the line
        $line->add(';')->add(new HardLineBreak());
    }
}
