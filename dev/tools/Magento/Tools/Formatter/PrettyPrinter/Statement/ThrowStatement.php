<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Throw;

class ThrowStatement extends AbstractControlStatement
{
    /**
     * This method constructs a new statement based on the specified throw.
     * @param PHPParser_Node_Stmt_Throw $node
     */
    public function __construct(PHPParser_Node_Stmt_Throw $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // add the class line
        $this->addToLine($treeNode, 'throw ');
        // add the arguments
        $treeNode = $this->resolveNode($this->node->expr, $treeNode);
        // add in the terminator
        $this->addToLine($treeNode, ';')->add(new HardLineBreak());
        return $treeNode;
    }
}
