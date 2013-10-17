<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_PropertyProperty;

class PropertyReference extends ReferenceAbstract
{
    /**
     * This method constructs a new reference based on the specified property.
     * @param PHPParser_Node_Stmt_PropertyProperty $node
     */
    public function __construct(PHPParser_Node_Stmt_PropertyProperty $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        /* Reference
        return '$' . $node->name
             . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
        */
        /** @var Line $line */
        $line = $treeNode->getData();
        // add the name to the end of the current line
        $line->add('$')->add($this->node->name);
        // optionally add in the default value
        if (null !== $this->node->default) {
            $line->add(' = ');
            $this->resolveNode($this->node->default, $treeNode);
        }
    }
}