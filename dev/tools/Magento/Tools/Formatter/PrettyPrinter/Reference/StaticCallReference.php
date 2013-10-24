<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\SimpleListLineBreak;
use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_StaticCall;
use PHPParser_Node_Expr;
use PHPParser_Node_Expr_ArrayDimFetch;
use PHPParser_Node_Expr_Variable;

class StaticCallReference extends AbstractReference
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
        parent::resolve($treeNode);
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
        $line = $treeNode->getData()->line;
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
            $line->add($this->node->name);
       // add the arguments
        $line->add('(');
        $this->processArgumentList($this->node->args, $treeNode, $line, new SimpleListLineBreak());
        $line->add(')');
        // TODO this probably needs to be in a more common place for other references as well.  Also,
        // the check for the current instance being a root node could be in a boolean method instead.
        if ($treeNode->getParent() instanceof Tree) {
            $line->add(';')->add(new HardLineBreak());
        }
    }
}
