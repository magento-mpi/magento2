<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Arg;

class ArgumentReference extends AbstractVariableReference
{
    /**
     * This method constructs a new statement based on the specified argument node.
     * @param PHPParser_Node_Arg $node
     */
    public function __construct(PHPParser_Node_Arg $node)
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
        // add the reference, if needed
        if ($this->node->byRef) {
            $this->addToLine($treeNode, '&');
        }
        // add in the actual variable reference
        return $this->resolveNode($this->node->value, $treeNode);
    }
}
