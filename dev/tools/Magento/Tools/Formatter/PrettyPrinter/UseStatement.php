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

class UseStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param \PHPParser_Node_Stmt $node
     */
    public function __construct(\PHPParser_Node_Stmt $node)
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
        if ($this->node instanceof \PHPParser_Node_Stmt_Use) {
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
                $tree->addSibling(new TreeNode($line));
                // process the name
                Printer::processStatement($use, $tree);
                // finish out the line
                $line->add(';')->add(new HardLineBreak());
            }
            // add a newline after the block
            $line = new Line(new HardLineBreak());
            $tree->addSibling(new TreeNode($line));
        } elseif ($this->node instanceof \PHPParser_Node_Stmt_UseUse) {
            /* Reference
            return $this->p($node->name)
                 . ($node->name->getLast() !== $node->alias ? ' as ' . $node->alias : '');
             */
            // process the name
            Printer::processStatement($this->node->name, $tree);
            // process the alias, if needed
            if ($this->node->name->getLast() !== $this->node->alias) {
                $line = $tree->getCurrentNode()->getData();
                $line->add(' as ')->add($this->node->alias);
            }
        }
    }
}