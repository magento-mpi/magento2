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
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        $line->add($this->node->value);
    }
}
