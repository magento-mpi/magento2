<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Exit;

class ExitReference extends AbstractFunctionReference
{
    /**
     * This method constructs a new statement based on the specified exit node.
     * @param PHPParser_Node_Expr_Exit $node
     */
    public function __construct(PHPParser_Node_Expr_Exit $node)
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
        // add in the exit
        $this->addToLine($treeNode, 'exit');
        // add the expression, if needed
        if ($this->node->expr) {
            $this->addToLine($treeNode, '(');
            $treeNode = $this->resolveNode($this->node->expr, $treeNode);
            $this->addToLine($treeNode, ')');
        }
        return $treeNode;
    }
}
