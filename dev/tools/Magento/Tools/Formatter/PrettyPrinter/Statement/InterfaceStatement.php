<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\ClassInterfaceLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Interface;

class InterfaceStatement  extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify interface node
     * @param PHPParser_Node_Stmt_Interface $node
     */
    public function __construct(PHPParser_Node_Stmt_Interface $node)
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
        return 'interface ' . $node->name
             . (!empty($node->extends) ? ' extends ' . $this->pCommaSeparated($node->extends) : '')
             . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
        */
        parent::resolve($treeNode);
        // add the class line
        $line = new Line();
        $line->add('interface ')->add($this->node->name);
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $treeNode->setData($line);
        // add in extends declaration
        if (!empty($this->node->extends)) {
            $line->add(' extends');
            $this->processArgumentList($this->node->extends, $treeNode, $line, new ClassInterfaceLineBreak());
        }
        $line->add(new HardLineBreak());
        // add the opening brace on a new line
        $treeNode = $treeNode->addSibling(new TreeNode((new Line('{'))->add(new HardLineBreak())));
        // processing the child nodes
        $this->processNodes($this->node->stmts, $treeNode);
        // add the closing brace on a new line
        $treeNode->addSibling(new TreeNode((new Line('}'))->add(new HardLineBreak())));
    }

    /**
     * This method processes the newly added node.
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param TreeNode $newNode Newly added node containing the statement
     * @param int $index 0 based index of the new node
     * @param int $total total number of nodes to be added
     * @return TreeNode Returns the originating node since just children are being added.
     */
    protected function processNode(TreeNode $originatingNode, TreeNode $newNode, $index, $total)
    {
        // this is called to add the member nodes to the class
        $originatingNode->addChild($newNode);
        // add a separator between all nodes
        if ($index < $total - 1) {
            $originatingNode->addChild(new TreeNode(new Line(new HardLineBreak())));
        }
        // always return the originating node
        return $originatingNode;
    }
}