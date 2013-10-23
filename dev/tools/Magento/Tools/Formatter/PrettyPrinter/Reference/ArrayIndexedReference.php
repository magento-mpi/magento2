<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ReferenceAbstract;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node;
use PHPParser_Node_Expr_ArrayDimFetch;
use PHPParser_Node_Expr_New;

class ArrayIndexedReference extends ReferenceAbstract
{
    /**
     * This method constructs a new statement based on the specified indexed array access.
     * @param PHPParser_Node_Expr_ArrayDimFetch $node
     */
    public function __construct(PHPParser_Node_Expr_ArrayDimFetch $node)
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
        return $this->pVarOrNewExpr($node->var)
             . '[' . (null !== $node->dim ? $this->p($node->dim) : '') . ']';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the variable
        $this->resolveVariable($this->node->var, $treeNode);
        // add in the index, which may not be specified
        $line->add('[');
        if (null !== $this->node->dim) {
            $this->resolveNode($this->node->dim, $treeNode);
        }
        $line->add(']');
    }

    /**
     * This method resolves the passed in node. If it is a special case of a new call, it is
     * surrounded with parenthesis.
     * @param PHPParser_Node $node Raw node being processed
     * @param TreeNode $treeNode
     */
    protected function resolveVariable(PHPParser_Node $node, TreeNode $treeNode) {
        if ($node instanceof PHPParser_Node_Expr_New) {
            /** @var Line $line */
            $line = $treeNode->getData()->line;
            // enclose new reference in parens
            $line->add('(');
            $this->resolveNode($node, $treeNode);
            $line->add(')');
        } else {
            // otherwise, just resolve the node
            $this->resolveNode($node, $treeNode);
        }
    }
}