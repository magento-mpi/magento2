<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\ConditionalLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_For;

class ForStatement extends AbstractLoopStatement
{
    /**
     * This method constructs a new statement based on the specified for statement.
     * @param PHPParser_Node_Stmt_For $node
     */
    public function __construct(PHPParser_Node_Stmt_For $node)
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
        return 'for ('
             . $this->pCommaSeparated($node->init) . ';' . (!empty($node->cond) ? ' ' : '')
             . $this->pCommaSeparated($node->cond) . ';' . (!empty($node->loop) ? ' ' : '')
             . $this->pCommaSeparated($node->loop)
             . ') {' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the namespace line
        $line->add('for (');
        // add in the init expression
        $lineBreak = new ConditionalLineBreak(array(array('')));
        $this->processArgumentList($this->node->init, $treeNode, $line, $lineBreak);
        $line->add(';');
        if (!empty($this->node->cond)) {
            $line->add(' ');
            $this->processArgumentList($this->node->cond, $treeNode, $line, $lineBreak);
        }
        $line->add(';');
        if (!empty($this->node->loop)) {
            $line->add(' ');
            $this->processArgumentList($this->node->loop, $treeNode, $line, $lineBreak);
        }
        // add in the rest
        $this->addBody($treeNode);
    }
}
