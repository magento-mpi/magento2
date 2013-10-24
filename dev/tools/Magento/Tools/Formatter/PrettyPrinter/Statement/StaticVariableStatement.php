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
use PHPParser_Node_Stmt_Static;

class StaticVariableStatement extends AbstractScriptStatement
{
    /**
     * This method constructs a new statement based on the specified static variable
     * @param PHPParser_Node_Stmt_Static $node
     */
    public function __construct(PHPParser_Node_Stmt_Static $node)
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
        return 'static ' . $this->pCommaSeparated($node->vars) . ';';
         */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the function line
        $line->add('static ');
        // add in the variables
        $this->processArgumentList($this->node->vars, $treeNode, $line, new SimpleListLineBreak());
        // add terminator
        $line->add(';')->add(new HardLineBreak());
    }
}