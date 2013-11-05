<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;

class AbstractVariableReference extends AbstractReference
{
    /**
     * This method adds the variable reference to the passed in line.
     * @param TreeNode $treeNode Node containing the current statement.
     * @param Line $line Line where the variable reference belongs.
     */
    protected function addVariableReference(TreeNode $treeNode, Line $line)
    {
        // add the name to the end of the current line
        $line->add('$')->add($this->node->name);
        // optionally add in the default value
        if (null !== $this->node->default) {
            $line->add(' = ');
            $this->resolveNode($this->node->default, $treeNode);
        }
    }
}
