<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_ClosureUse;

class ClosureUseReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specified expression.
     * @param PHPParser_Node_Expr_ClosureUse $node
     */
    public function __construct(PHPParser_Node_Expr_ClosureUse $node)
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
        if ($this->node->byRef) {
            $this->addToLine($treeNode, '&');
        }
        $this->addToLine($treeNode, '$')->add($this->node->var);
        return $treeNode;
    }
}
