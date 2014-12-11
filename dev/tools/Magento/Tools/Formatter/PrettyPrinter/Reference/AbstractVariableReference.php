<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;

class AbstractVariableReference extends AbstractReference
{
    /**
     * This method adds the variable reference to the passed in line.
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    protected function addVariableReference(TreeNode $treeNode)
    {
        // add the name to the end of the current line
        $this->addToLine($treeNode, '$')->add($this->node->name);
        // optionally add in the default value
        if (null !== $this->node->default) {
            $this->addToLine($treeNode, ' = ');
            $treeNode = $this->resolveNode($this->node->default, $treeNode);
        }
        return $treeNode;
    }
}
