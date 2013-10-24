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
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Elseif;

class ElseIfStatement extends AbstractConditionalStatement
{
    /**
     * This method constructs a new statement based on the specified elseif statement.
     * @param PHPParser_Node_Stmt_Elseif $node
     */
    public function __construct(PHPParser_Node_Stmt_Elseif $node)
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
        return ' elseif (' . $this->p($node->cond) . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the if line
        $line->add('} elseif (');
        $this->resolveNode($this->node->cond, $treeNode);
        $line->add(') {')->add(new HardLineBreak());
        // processing the child nodes
        $this->processNodes($this->node->stmts, $treeNode);
    }
}
