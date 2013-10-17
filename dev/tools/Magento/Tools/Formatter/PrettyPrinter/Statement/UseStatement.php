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
use PHPParser_Node_Stmt_Use;

class UseStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specified use clause.
     * @param PHPParser_Node_Stmt_Use $node
     */
    public function __construct(PHPParser_Node_Stmt_Use $node)
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
        $result = '';
        // loop through and place each use on a line
        foreach ($node->uses as $use) {
            $result .= 'use ' . $this->p($use) . ';' . self::EOL;
        }
        return $result;
         */
        // loop through and place each use on a line
        foreach ($this->node->uses as $use) {
            // add the line to the tree
            $line = new Line('use ');
            // add the line prior to current node only out of convenience
            $useTreeNode = $treeNode->addSibling(new TreeNode($line), false);
            // process the name
            $this->resolveNode($use, $useTreeNode);
            // finish out the line
            $line->add(';')->add(new HardLineBreak());
        }
        // add a newline after the block
        $line = new Line(new HardLineBreak());
        // replace the statement with the line since it is resolved
        $treeNode->setData($line);
    }
}