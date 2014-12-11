<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_ArrayDimFetch;

class ArrayIndexedReference extends AbstractFunctionReference
{
    /**
     * This method constructs a new statement based on the specified indexed array access.
     * @param PHPParser_Node_Expr_ArrayDimFetch $node
     */
    public function __construct(PHPParser_Node_Expr_ArrayDimFetch $node)
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
        // add the variable
        $this->resolveVariable($this->node->var, $treeNode);
        // add in the index, which may not be specified
        $this->addToLine($treeNode, '[');
        if (null !== $this->node->dim) {
            $this->resolveNode($this->node->dim, $treeNode);
        }
        $this->addToLine($treeNode, ']');
        return $treeNode;
    }
}
