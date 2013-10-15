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

/**
 * This class represents a class statement.
 */
class ClassStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param \PHPParser_Node_Stmt_Class $node
     */
    public function __construct(\PHPParser_Node_Stmt_Class $node)
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
        return $this->pModifiers($node->type)
             . 'class ' . $node->name
             . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
             . (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '')
             . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
         */
        // add the comments from the current node
        $this->addComments($tree);
        // add the class line
        $line = new Line();
        $this->addModifier($this->node->type, $line);
        $line->add('class ')->add($this->node->name);
        $tree->addSibling(new TreeNode($line));
        // add in extends declaration
        if (!empty($this->node->extends)) {
            $line->add(' extends ');
            Printer::processStatement($this->node->extends, $tree);
        }
        // add in the implement declarations
        if (!empty($this->node->implements)) {
            $line->add(' implements');
            $this->processArgumentList($this->node->implements, $tree, $line);
        }
        $line->add(new HardLineBreak());
        // add the opening brace on a new line
        $tree->addSibling(new TreeNode((new Line('{'))->add(new HardLineBreak())));
        // add the closing brace on a new line
        $tree->addSibling(new TreeNode((new Line('}'))->add(new HardLineBreak())));
    }
}
