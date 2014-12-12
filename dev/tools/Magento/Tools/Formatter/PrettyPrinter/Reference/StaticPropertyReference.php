<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr;
use PHPParser_Node_Expr_StaticPropertyFetch;

class StaticPropertyReference extends AbstractPropertyReference
{
    /**
     * This method constructs a new reference based on the specified constant.
     * @param PHPParser_Node_Expr_StaticPropertyFetch $node
     */
    public function __construct(PHPParser_Node_Expr_StaticPropertyFetch $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current reference, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // add the class reference
        $this->resolveNode($this->node->class, $treeNode);
        // add in the actual reference
        $this->addToLine($treeNode, '::$');
        // if the name is an expression, then use the framework to resolve
        if ($this->node->name instanceof PHPParser_Node_Expr) {
            $treeNode = $this->resolveNode($this->node->name, $treeNode);
        } else {
            // otherwise, just use the name
            $this->addToLine($treeNode, $this->node->name);
        }
        return $treeNode;
    }
}
