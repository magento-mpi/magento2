<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_ClassConstFetch;

class ClassConstantReference extends AbstractPropertyReference
{
    /**
     * This method constructs a new statement based on the specified argument node.
     * @param PHPParser_Node_Expr_ClassConstFetch $node
     */
    public function __construct(PHPParser_Node_Expr_ClassConstFetch $node)
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
        // add the class reference
        $this->resolveNode($this->node->class, $treeNode);
        // add in the actual reference
        $this->addToLine($treeNode, '::')->add($this->node->name);
        return $treeNode;
    }
}
