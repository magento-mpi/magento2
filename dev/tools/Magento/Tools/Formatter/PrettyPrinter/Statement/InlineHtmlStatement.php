<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\IndentConsumer;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_InlineHTML;

class InlineHtmlStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified statement.
     * @param PHPParser_Node_Stmt_InlineHTML $node
     */
    public function __construct(PHPParser_Node_Stmt_InlineHTML $node)
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
        // assume in the context of php, so close it
        $this->addToLine($treeNode, new IndentConsumer())->add('?>')->add(new HardLineBreak());
        // print the HTML
        $this->addToLine($treeNode, new IndentConsumer())->add($this->node->value);
        // go back to PHP
        $this->addToLine($treeNode, new IndentConsumer())->add('<?php')->add(new HardLineBreak());
        return $treeNode;
    }
}
