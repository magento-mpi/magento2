<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_ArrayItem;

class ArrayItemReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specified expression.
     * @param PHPParser_Node_Expr_ArrayItem $node
     */
    public function __construct(PHPParser_Node_Expr_ArrayItem $node)
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
        // add the array item to the end of the current line
        if (null !== $this->node->key) {
            $treeNode = $this->resolveNode($this->node->key, $treeNode);
            $this->addToLine($treeNode, ' => ');
        }
        if ($this->node->byRef) {
            $this->addToLine($treeNode, '&');
        }
        return $this->resolveNode($this->node->value, $treeNode);
    }
}
