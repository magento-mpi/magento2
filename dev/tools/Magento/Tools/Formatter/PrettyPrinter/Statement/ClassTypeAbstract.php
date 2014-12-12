<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;

abstract class ClassTypeAbstract extends AbstractStatement
{
    /**
     * This method adds the body of the class to the tree.
     *
     * @param TreeNode $treeNode Node to used as the sibling of the opening brace.
     * @return TreeNode
     */
    protected function addBody(TreeNode $treeNode)
    {
        // add the opening brace on a new line
        $treeNode = $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('{'))->add(new HardLineBreak())));
        // processing the child nodes
        $this->processNodes($this->node->stmts, $treeNode);
        // add the closing brace on a new line
        return $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
    }

    /**
     * This method processes the newly added node.
     *
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param TreeNode $newNode Newly added node containing the statement
     * @param int $index 0 based index of the new node
     * @param int $total total number of nodes to be added
     * @param mixed $data Data that is passed to derived class when processing the node.
     * @return TreeNode Returns the originating node since just children are being added.
     */
    protected function processNode(TreeNode $originatingNode, TreeNode $newNode, $index, $total, $data = null)
    {
        // this is called to add the member nodes to the class
        $originatingNode->addChild($newNode);
        // add a separator between all nodes
        if ($index < $total - 1) {
            $originatingNode->addChild(AbstractSyntax::getNodeLine(new Line(new HardLineBreak())));
        }
        // always return the originating node
        return $originatingNode;
    }

    /**
     * We should trim these comments
     *
     * @return bool
     */
    public function isTrimComments()
    {
        return true;
    }
}
