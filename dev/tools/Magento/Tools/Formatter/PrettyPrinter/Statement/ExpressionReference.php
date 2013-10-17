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
use PHPParser_Node_Expr;
use PHPParser_Node_Expr_Variable;

class ExpressionReference extends ReferenceAbstract
{
    /**
     * This method constructs a new statement based on the specify expression
     * @param PHPParser_Node_Expr_Variable $node
     */
    public function __construct(PHPParser_Node_Expr_Variable $node)
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
        if ($node->name instanceof PHPParser_Node_Expr) {
            return '${' . $this->p($node->name) . '}';
        } else {
            return '$' . $node->name;
        }
        */
        /** @var Line $line */
        $line = $treeNode->getData();
        // add the expression to the end of the current line
        $line->add('$');
        if ($this->node->name instanceof PHPParser_Node_Expr) {
            $line->add('{');
            // add in the value as a node
            $this->resolveNode($this->node->name, $treeNode);
            $line->add('}');
        } else {
            $line->add($this->node->name);
        }
    }
}