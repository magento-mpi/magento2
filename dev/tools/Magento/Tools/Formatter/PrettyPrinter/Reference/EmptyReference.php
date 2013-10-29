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
use PHPParser_Node_Expr_Empty;

class EmptyReference extends AbstractFunctionReference
{
    /**
     * This method constructs a new statement based on the specified argument node.
     * @param PHPParser_Node_Expr_Empty $node
     */
    public function __construct(PHPParser_Node_Expr_Empty $node)
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
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add in the empty statement
        $line->add('empty(');
        // add in the actual variable reference
        $this->resolveNode($this->node->expr, $treeNode);
        // add in the closer
        $line->add(')');
    }
}
