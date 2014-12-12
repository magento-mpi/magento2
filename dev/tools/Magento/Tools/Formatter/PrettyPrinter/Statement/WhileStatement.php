<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_While;

class WhileStatement extends AbstractLoopStatement
{
    /**
     * This method constructs a new statement based on the specified for statement.
     * @param PHPParser_Node_Stmt_While $node
     */
    public function __construct(PHPParser_Node_Stmt_While $node)
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
        $this->addToLine($treeNode, 'while (');
        // add in the condition
        $treeNode = $this->resolveNode($this->node->cond, $treeNode);
        // add in the rest
        return $this->addBody($treeNode);
    }
}
