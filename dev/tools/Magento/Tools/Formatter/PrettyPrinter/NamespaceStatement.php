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

class NamespaceStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param \PHPParser_Node_Stmt_Class $node
     */
    public function __construct(\PHPParser_Node_Stmt_Namespace $node)
    {
        parent::__construct($node);
    }

    /**
     * This method is used to process the current node.
     *
     * @param Tree $tree
     */
    public function process(Tree $tree)
    {
        /* Reference
        if ($this->canUseSemicolonNamespaces) {
            return 'namespace ' . $this->p($node->name) . ';' . "\n\n" . $this->pStmts($node->stmts, false);
        } else {
            return 'namespace' . (null !== $node->name ? ' ' . $this->p($node->name) : '')
                 . ' {' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
        }
         */
        // add the comments from the current node
        $this->addComments($tree);
        // add the class line
        $line = new Line('namespace ');
        $tree->addSibling(new TreeNode($line));
        Printer::processStatement($this->node->name, $tree);

        $line->add(';')->add(new HardLineBreak())->add(new HardLineBreak());

        Printer::processStatements($this->node->stmts, $tree);
    }
}