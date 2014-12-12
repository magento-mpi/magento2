<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Unset;

class UnsetStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified unset.
     * @param PHPParser_Node_Stmt_Unset $node
     */
    public function __construct(PHPParser_Node_Stmt_Unset $node)
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
        // add the class line
        $this->addToLine($treeNode, 'unset');
        // add the arguments
        $this->processArgsList($this->node->vars, $treeNode, new CallLineBreak());
        // add in the terminator
        $this->addToLine($treeNode, ';')->add(new HardLineBreak());
        return $treeNode;
    }
}
