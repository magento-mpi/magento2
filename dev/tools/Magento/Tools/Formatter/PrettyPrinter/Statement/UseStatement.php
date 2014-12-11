<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Use;

class UseStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified use clause.
     *
     * @param PHPParser_Node_Stmt_Use $node
     */
    public function __construct(PHPParser_Node_Stmt_Use $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     *
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // loop through and place each use on a line
        foreach ($this->node->uses as $use) {
            // add the line to the tree
            $line = new Line('use ');
            // add the line prior to current node only out of convenience
            $useTreeNode = $treeNode->addSibling(AbstractSyntax::getNodeLine($line), false);
            // process the name
            $this->resolveNode($use, $useTreeNode);
            // finish out the line
            $this->addToLine($useTreeNode, ';')->add(new HardLineBreak());
        }
        // add a newline after the block
        $this->addToLine($treeNode, new HardLineBreak());
        return $treeNode;
    }

    /**
     * {@inheritdoc}
     */
    public function isTrimComments()
    {
        return true;
    }
}
