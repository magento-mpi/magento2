<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_While;

class WhileStatement extends AbstractLoopStatement
{
    /**
     * This method constructs a new statement based on the specified for statement.
     * @param PHPParser_Node_Stmt_While $node
     */
    public function __construct(PHPParser_Node_Stmt_While $node)
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
        return 'while (' . $this->p($node->cond) . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the namespace line
        $line->add('while (');
        // add in the condition
        $this->resolveNode($this->node->cond, $treeNode);
        // add in the rest
        $this->addBody($treeNode);
    }
}