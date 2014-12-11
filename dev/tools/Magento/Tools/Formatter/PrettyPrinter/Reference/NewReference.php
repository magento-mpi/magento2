<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_New;

class NewReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specified new reference.
     * @param PHPParser_Node_Expr_New $node
     */
    public function __construct(PHPParser_Node_Expr_New $node)
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
        // add in the new statement
        $this->addToLine($treeNode, 'new ');
        // add in the class reference
        $treeNode = $this->resolveNode($this->node->class, $treeNode);
        // add in the arguments
        return $this->processArgsList($this->node->args, $treeNode, new CallLineBreak());
    }
}
