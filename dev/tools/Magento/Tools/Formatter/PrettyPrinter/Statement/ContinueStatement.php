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
use PHPParser_Node_Stmt_Continue;

class ContinueStatement extends AbstractControlStatement
{
    /**
     * This method constructs a new statement based on the specify break node.
     * @param PHPParser_Node_Stmt_Continue $node
     */
    public function __construct(PHPParser_Node_Stmt_Continue $node)
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
        return 'continue' . ($node->num !== null ? ' ' . $this->p($node->num) : '') . ';';
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the const line
        $line->add('continue');
        // add in the break number, if specified
        if (null !== $this->node->num) {
            $this->resolveNode($this->node->num, $treeNode);
        }
        // terminate the line
        $line->add(';')->add(new HardLineBreak());
    }
}