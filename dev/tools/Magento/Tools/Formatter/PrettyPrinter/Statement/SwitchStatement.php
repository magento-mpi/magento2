<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
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
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /* Reference
        return 'switch (' . $this->p($node->cond) . ') {'
             . "\n" . $this->pStmts($node->cases) . "\n" . '}';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the control word
        $line->add('switch (');
        // add in the condition
        $this->resolveNode($this->node->cond, $treeNode);
        $line->add(') {')->add(new HardLineBreak());
        // processing the case nodes as children
        $this->processNodes($this->node->cases, $treeNode);
        // add the closing brace on a new line
        $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
    }
}
