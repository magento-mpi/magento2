<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Array;

class ArrayReference extends ReferenceAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Expr_Array $node
     */
    public function __construct(PHPParser_Node_Expr_Array $node)
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
        return 'array(' . $this->pCommaSeparated($node->items) . ')';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the array to the end of the current line
        $line->add('array(');
        if (count($this->node->items) > 0) {
            $lineBreak = new CallLineBreak();
            $this->processArgumentList($this->node->items, $treeNode, $line, $lineBreak);
            $line->add($lineBreak);
        }
        $line->add(')');
    }
}