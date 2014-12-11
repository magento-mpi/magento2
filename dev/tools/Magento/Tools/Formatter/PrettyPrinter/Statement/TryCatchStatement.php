<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_TryCatch;

class TryCatchStatement extends AbstractControlStatement
{
    /**
     * This method constructs a new statement based on the specified try node.
     * @param PHPParser_Node_Stmt_TryCatch $node
     */
    public function __construct(PHPParser_Node_Stmt_TryCatch $node)
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
        // add the try line
        $this->addToLine($treeNode, 'try {')->add(new HardLineBreak());
        // add in the statements inside the try
        $this->processNodes($this->node->stmts, $treeNode, true);
        // add in the catches for this try
        $treeNode = $this->processNodes($this->node->catches, $treeNode, false);
        // add in the finally
        if (null !== $this->node->finallyStmts) {
            $line = (new Line('} finally {'))->add(new HardLineBreak());
            $treeNode = $treeNode->addSibling(AbstractSyntax::getNodeLine($line));
            $this->processNodes($this->node->finallyStmts, $treeNode, true);
        }
        // add the closing brace on a new line
        return $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
    }

    /**
     * This method processes the newly added node.
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param TreeNode $newNode Newly added node containing the statement
     * @param int $index 0 based index of the new node
     * @param int $total total number of nodes to be added
     * @param mixed $data Data that is passed to derived class when processing the node.
     * @return TreeNode Returns the originating node since just children are being added.
     */
    protected function processNode(TreeNode $originatingNode, TreeNode $newNode, $index, $total, $data = null)
    {
        if ($data) {
            // adding the statements inside the try, so add them as children
            $originatingNode->addChild($newNode);
        } else {
            // this is called to add the catches and finally nodes; therefore, add it as a sibling
            // to the originating node
            $originatingNode = $originatingNode->addSibling($newNode);
        }
        return $originatingNode;
    }
}
