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
use PHPParser_Node_Stmt_StaticVar;

class StaticVariableReference extends AbstractVariableReference
{
    /**
     * This method constructs a new reference based on the specified property.
     * @param PHPParser_Node_Stmt_StaticVar $node
     */
    public function __construct(PHPParser_Node_Stmt_StaticVar $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /* Reference
        return '$' . $node->name
             . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add in the variable reference
        $this->addVariableReference($treeNode, $line);
    }
}