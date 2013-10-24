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
use PHPParser_Node_Stmt_Case;

class CaseStatement extends AbstractConditionalStatement
{
    /**
     * This method constructs a new statement based on the specified case statement.
     * @param PHPParser_Node_Stmt_Case $node
     */
    public function __construct(PHPParser_Node_Stmt_Case $node)
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
        return (null !== $node->cond ? 'case ' . $this->p($node->cond) : 'default') . ':'
             . ($node->stmts ? "\n" . $this->pStmts($node->stmts) : '');
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the control word
        if (null !== $this->node->cond) {
            $line->add('case ');
            $this->resolveNode($this->node->cond, $treeNode);
        } else {
            $line->add('default');
        }
        $line->add(':')->add(new HardLineBreak());
        // add in the statements
        $this->processNodes($this->node->stmts, $treeNode);
    }
}
