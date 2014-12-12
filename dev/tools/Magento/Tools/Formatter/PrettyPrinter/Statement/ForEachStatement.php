<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Foreach;

class ForEachStatement extends AbstractLoopStatement
{
    /**
     * This method constructs a new statement based on the specified foreach statement.
     * @param PHPParser_Node_Stmt_Foreach $node
     */
    public function __construct(PHPParser_Node_Stmt_Foreach $node)
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
        // add the namespace line
        $this->addToLine($treeNode, 'foreach (');
        // add in the collection
        $treeNode = $this->resolveNode($this->node->expr, $treeNode);
        $this->addToLine($treeNode, ' as ');
        // add in the key, if specified
        if (null !== $this->node->keyVar) {
            $treeNode = $this->resolveNode($this->node->keyVar, $treeNode);
            $this->addToLine($treeNode, ' => ');
        }
        if ($this->node->byRef) {
            $this->addToLine($treeNode, '&');
        }
        $treeNode = $this->resolveNode($this->node->valueVar, $treeNode);
        // add in the rest
        return $this->addBody($treeNode);
    }
}
