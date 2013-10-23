<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\SimpleListLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_StaticCall;

class StaticCallReference extends ReferenceAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Expr_StaticCall $node
     */
    public function __construct(PHPParser_Node_Expr_StaticCall $node)
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
        return $this->p($node->class) . '::'
            . ($node->name instanceof PHPParser_Node_Expr
              ? $node->name instanceof PHPParser_Node_Expr_Variable ||
                $node->name instanceof PHPParser_Node_Expr_ArrayDimFetch
                ? $this->p($node->name)
                : '{' . $this->p($node->name) . '}'
              : $node->name)
            . '(' . $this->getParametersForCall($node->args) . ')';
        */
        /** @var Line $line */
        $line = $treeNode->getData();
        $line->add('::');
        $this->resolveNode($this->node->class, $treeNode);
        if ($this->node->name instanceof PHPParser_Node_Expr) {
            if ($this->node->name instanceof PHPParser_Node_Expr_Variable ||
                $this->node->name instanceof PHPParser_Node_Expr_ArrayDimFetch) {
                // add in the value as a node
                $this->resolveNode($this->node->name, $treeNode);
            }
            else {
                $line->add('{');
                $this->resolveNode($this->node->name, $treeNode);
                $line->add('}');
            }
        }
        else
            $this->node->name;

        // add the arguments
        $line->add('(');
        $this->processArgumentList($this->node->args, $treeNode, $line, new SimpleListLineBreak());
        $line->add(')');
    }
}