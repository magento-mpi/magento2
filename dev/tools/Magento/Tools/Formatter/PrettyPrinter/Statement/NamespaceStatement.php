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
use PHPParser_Node_Stmt_Namespace;

class NamespaceStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified namespace.
     * @param PHPParser_Node_Stmt_Namespace $node
     */
    public function __construct(PHPParser_Node_Stmt_Namespace $node)
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
        // add the namespace line
        $line->add('namespace ');
        // finish out the line
        $this->resolveNode($this->node->name, $treeNode);
        $line->add(';')->add(new HardLineBreak())->add(new HardLineBreak());
        // child nodes of namespace are at the same level as namespace
        $this->processNodes($this->node->stmts, $treeNode);
    }

    /**
     * This method processes the newly added node.
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param TreeNode $newNode Newly added node containing the statement
     * @param int $index 0 based index of the new node
     * @param int $total total number of nodes to be added
     * @param mixed $data Data that is passed to derived class when processing the node.
     * @return TreeNode Returns the newly added node.
     */
    protected function processNode(TreeNode $originatingNode, TreeNode $newNode, $index, $total, $data = null)
    {
        // this is called to add the use and class lines
        return $originatingNode->addSibling($newNode);
    }

    /**
     * We should trim these comments
     * @return bool
     */
    public function isTrimComments()
    {
        return true;
    }
}
