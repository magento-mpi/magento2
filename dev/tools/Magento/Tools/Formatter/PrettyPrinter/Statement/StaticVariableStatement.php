<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
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
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // add the function line
        $this->addToLine($treeNode, 'static ');
        // add in the variables
        $this->processArgumentList($this->node->vars, $treeNode, new SimpleListLineBreak());
        // add terminator
        $this->addToLine($treeNode, ';')->add(new HardLineBreak());
        return $treeNode;
    }
}
