<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_FuncCall;

class FunctionCall extends AbstractFunctionReference
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Expr_FuncCall $node
     */
    public function __construct(PHPParser_Node_Expr_FuncCall $node)
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
        $this->resolveNode($this->node->name, $treeNode);
        // add in the argument call
        return $this->processArgsList($this->node->args, $treeNode, $line, new CallLineBreak());
    }
}
