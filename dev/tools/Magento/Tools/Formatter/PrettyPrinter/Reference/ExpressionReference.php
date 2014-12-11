<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr;
use PHPParser_Node_Expr_Variable;

class ExpressionReference extends AbstractVariableReference
{
    /**
     * This method constructs a new statement based on the specified expression.
     * @param PHPParser_Node_Expr_Variable $node
     */
    public function __construct(PHPParser_Node_Expr_Variable $node)
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
        // add the expression to the end of the current line
        $this->addToLine($treeNode, '$');
        if ($this->node->name instanceof PHPParser_Node_Expr) {
            $this->addToLine($treeNode, '{');
            // add in the value as a node
            $treeNode = $this->resolveNode($this->node->name, $treeNode);
            $this->addToLine($treeNode, '}');
        } else {
            $this->addToLine($treeNode, $this->node->name);
        }
        return $treeNode;
    }
}
