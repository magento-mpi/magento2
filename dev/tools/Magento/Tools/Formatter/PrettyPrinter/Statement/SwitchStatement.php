<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Switch;

class SwitchStatement extends AbstractConditionalStatement
{
    /**
     * This method constructs a new statement based on the specified if statement.
     * @param PHPParser_Node_Stmt_Switch $node
     */
    public function __construct(PHPParser_Node_Stmt_Switch $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // add the control word
        $this->addToLine($treeNode, 'switch (');
        // add in the condition
        $treeNode = $this->resolveNode($this->node->cond, $treeNode);
        $this->addToLine($treeNode, ') {')->add(new HardLineBreak());
        // processing the case nodes as children
        $this->processNodes($this->node->cases, $treeNode);
        // add the closing brace on a new line
        return $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
    }
}
