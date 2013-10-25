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
use Magento\Tools\Formatter\PrettyPrinter\SimpleListLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Echo;

class EchoStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Stmt_Echo $node
     */
    public function __construct(PHPParser_Node_Stmt_Echo $node)
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
        return 'echo ' . $this->pCommaSeparated($node->exprs) . ';';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the class line
        $line->add('echo ');
        // add the arguments
        $this->processArgumentList($this->node->exprs, $treeNode, $line, new SimpleListLineBreak());
        // add in the terminator
        $line->add(';')->add(new HardLineBreak());
    }
}