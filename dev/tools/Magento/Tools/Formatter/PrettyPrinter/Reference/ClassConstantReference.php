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
use PHPParser_Node_Expr_ClassConstFetch;

class ClassConstantReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specified argument node.
     * @param PHPParser_Node_Expr_ClassConstFetch $node
     */
    public function __construct(PHPParser_Node_Expr_ClassConstFetch $node)
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
        return $this->p($node->class) . '::' . $node->name;
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the class reference
        $this->resolveNode($this->node->class, $treeNode);
        // add in the actual reference
        $line->add('::')->add($this->node->name);
    }
}