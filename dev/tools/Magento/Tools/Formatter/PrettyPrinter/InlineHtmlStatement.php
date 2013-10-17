<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

class InlineHtmlStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param \PHPParser_Node_Stmt_InlineHTML $node
     */
    public function __construct(\PHPParser_Node_Stmt_InlineHTML $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        /* Reference
        return '?>' . $this->pNoIndent("\n" . $node->value) . '<?php ';
         */
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $treeNode->setData(new Line($this->node->value));
    }
}
