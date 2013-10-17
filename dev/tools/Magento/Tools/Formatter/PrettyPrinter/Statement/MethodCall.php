<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */


namespace Magento\Tools\Formatter\PrettyPrinter\Statement;


use Magento\Tools\Formatter\PrettyPrinter\ConditionalLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr;
use PHPParser_Node_Expr_MethodCall;

class MethodCall extends ReferenceAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Expr_MethodCall $node
     */
    public function __construct(PHPParser_Node_Expr_MethodCall $node)
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
        return $this->pVarOrNewExpr($node->var) . '->' . $this->pObjectProperty($node->name)
             . '(' . $this->pCommaSeparated($node->args) . ')';
        */
        /** @var Line $line */
        $line = $treeNode->getData();
        // add the expression to the end of the current line
        $this->resolveNode($this->node->var, $treeNode);
        $line->add(new ConditionalLineBreak(''))->add('->');
        // if the name is an expression, then use the framework to resolve
        if ($this->node->name instanceof PHPParser_Node_Expr) {
            $this->resolveNode($this->node->name, $treeNode);
        } else {
            // otherwise, just use the name
            $line->add($this->node->name);
        }
        // add in the argument call
        $line->add('(');
        $this->processArgumentList($this->node->args, $treeNode, $line);
        $line->add(')');
    }
}