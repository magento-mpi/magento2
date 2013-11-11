<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\WrapperLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;

abstract class AbstractConditionalStatement extends AbstractStatement
{
    /**
     * This method add in the conditional and children for the node (i.e. for if's and elseif's).
     * @param TreeNode $treeNode Node containing the current statement.
     * @param string $keyword String containing the keyword for the statement.
     */
    protected function addConditional(TreeNode $treeNode, $keyword)
    {
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the if line
        $lineBreak = new WrapperLineBreak();
        $line->add($keyword)->add(' (')->add($lineBreak);
        // add in the condition
        $this->resolveNode($this->node->cond, $treeNode);
        $line->add($lineBreak)->add(') {')->add(new HardLineBreak());
        // processing the child nodes
        $this->processNodes($this->node->stmts, $treeNode, true);
    }
}
