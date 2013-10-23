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
use Magento\Tools\Formatter\PrettyPrinter\ParameterLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Function;

class FunctionStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specified function
     * @param PHPParser_Node_Stmt_Function $node
     */
    public function __construct(PHPParser_Node_Stmt_Function $node)
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
        return 'function ' . ($node->byRef ? '&' : '') . $node->name
             . '(' . $this->pCommaSeparated($node->params) . ')'
             . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
         */
        // add the class line
        $line = new Line();
        $line->add('function ');
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $treeNode->setData($line);
        if ($this->node->byRef) {
            $line->add('&');
        }
        // add in the name and parameters
        $line->add($this->node->name)->add('(');
        $lineBreak = new ParameterLineBreak();
        $this->processArgumentList($this->node->params, $treeNode, $line, $lineBreak);
        $line->add($lineBreak);
        $line->add(')')->add($lineBreak)->add('{')->add(new HardLineBreak());
        // process content of the methods
        $this->processNodes($this->node->stmts, $treeNode);
        // add closing block
        $treeNode->addSibling(new TreeNode((new Line('}'))->add(new HardLineBreak())));
    }
}