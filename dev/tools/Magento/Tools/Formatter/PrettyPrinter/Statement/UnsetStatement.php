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
use PHPParser_Node_Stmt_Unset;

class UnsetStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified unset.
     * @param PHPParser_Node_Stmt_Unset $node
     */
    public function __construct(PHPParser_Node_Stmt_Unset $node)
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
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the class line
        $line->add('unset(');
        // add the arguments
        $this->processArgumentList($this->node->vars, $treeNode, $line, new SimpleListLineBreak());
        // add in the terminator
        $line->add(');')->add(new HardLineBreak());
    }
}
