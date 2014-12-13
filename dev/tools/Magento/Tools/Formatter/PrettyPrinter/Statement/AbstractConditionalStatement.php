<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\WrapperLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;

abstract class AbstractConditionalStatement extends AbstractStatement
{
    /**
     * This method add in the conditional and children for the node (i.e. for if's and elseif's).
     * @param TreeNode $treeNode Node containing the current statement.
     * @param string $keyword String containing the keyword for the statement.
     * @return TreeNode
     */
    protected function addConditional(TreeNode $treeNode, $keyword)
    {
        // add the keyword line
        $lineBreak = new WrapperLineBreak();
        $this->addToLine($treeNode, $keyword)->add(' (')->add($lineBreak);
        // add in the condition
        $treeNode = $this->resolveNode($this->node->cond, $treeNode);
        $this->addToLine($treeNode, $lineBreak)->add(') {')->add(new HardLineBreak());
        // processing the child nodes
        $this->processNodes($this->node->stmts, $treeNode, true);
        return $treeNode;
    }
}
