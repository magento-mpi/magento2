<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Isset;

class IssetReference extends AbstractFunctionReference
{
    /**
     * This method constructs a new statement based on the specified argument node.
     * @param PHPParser_Node_Expr_Isset $node
     */
    public function __construct(PHPParser_Node_Expr_Isset $node)
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
        $this->addToLine($treeNode, 'isset');
        // add in the actual variable reference
        return $this->processArgsList($this->node->vars, $treeNode, new CallLineBreak());
    }
}
