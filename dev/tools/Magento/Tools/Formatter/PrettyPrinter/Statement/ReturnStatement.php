<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Return;

class ReturnStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified return
     * @param PHPParser_Node_Stmt_Return $node
     */
    public function __construct(PHPParser_Node_Stmt_Return $node)
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
        // add the return line
        $this->addToLine($treeNode, 'return');
        // add in the express, if available
        if (null !== $this->node->expr) {
            $this->addToLine($treeNode, ' ');
            $treeNode = $this->resolveNode($this->node->expr, $treeNode);
        }
        // terminate the line
        $this->addToLine($treeNode, ';')->add(new HardLineBreak());
        return $treeNode;
    }
}
