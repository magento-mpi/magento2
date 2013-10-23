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
use PHPParser_Node_Stmt_Foreach;

class ForEachStatement extends AbstractLoopStatement
{
    /**
     * This method constructs a new statement based on the specified foreach statement.
     * @param PHPParser_Node_Stmt_Foreach $node
     */
    public function __construct(PHPParser_Node_Stmt_Foreach $node)
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
        return 'foreach (' . $this->p($node->expr) . ' as '
             . (null !== $node->keyVar ? $this->p($node->keyVar) . ' => ' : '')
             . ($node->byRef ? '&' : '') . $this->p($node->valueVar) . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the namespace line
        $line->add('foreach (');
        // add in the collection
        $this->resolveNode($this->node->expr, $treeNode);
        $line->add(' as ');
        // add in the key, if specified
        if (null !== $this->node->keyVar) {
            $this->resolveNode($this->node->keyVar, $treeNode);
            $line->add(' => ');
        }
        if ($this->node->byRef) {
            $line->add('&');
        }
        $this->resolveNode($this->node->valueVar, $treeNode);
        // add in the rest
        $this->addBody($treeNode);
    }
}
