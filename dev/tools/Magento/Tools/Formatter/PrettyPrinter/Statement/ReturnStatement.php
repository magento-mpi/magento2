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
use PHPParser_Node_Stmt_Return;

class ReturnStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified return
     * @param PHPParser_Node_Stmt_Return $node
     */
    public function __construct(PHPParser_Node_Stmt_Return $node)
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
        return 'return' . (null !== $node->expr ? ' ' . $this->p($node->expr) : '') . ';';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the return line
        $line->add('return');
        // add in the express, if available
        if (null !== $this->node->expr) {
            $line->add(' ');
            $this->resolveNode($this->node->expr, $treeNode);
        }
        // terminate the line
        $line->add(';')->add(new HardLineBreak());
    }
}