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
use PHPParser_Node_Stmt_ClassConst;

class ConstantStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Stmt_ClassConst $node
     */
    public function __construct(PHPParser_Node_Stmt_ClassConst $node)
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
        return 'const ' . $this->pCommaSeparated($node->consts) . ';';
        */
        // add the const line
        $line = new Line('const ');
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $treeNode->setData($line);
        // add in the list of actual constants
        $this->processArgumentList($this->node->consts, $treeNode, $line, new SimpleListLineBreak());
        // terminate the line
        $line->add(';')->add(new HardLineBreak());
    }
}