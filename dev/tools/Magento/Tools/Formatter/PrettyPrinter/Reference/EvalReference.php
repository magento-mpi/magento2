<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Eval;

class EvalReference extends AbstractFunctionReference
{
    /**
     * This method constructs a new statement based on the specified argument node.
     * @param PHPParser_Node_Expr_Eval $node
     */
    public function __construct(PHPParser_Node_Expr_Eval $node)
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
        // add in the empty statement
        $this->addToLine($treeNode, 'eval(');
        // add in the actual statment reference
        $treeNode = $this->resolveNode($this->node->expr, $treeNode);
        // add in the closer
        $this->addToLine($treeNode, ')');
        return $treeNode;
    }
}
