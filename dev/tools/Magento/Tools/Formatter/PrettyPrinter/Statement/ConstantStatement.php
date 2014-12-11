<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\SimpleListLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_ClassConst;

class ConstantStatement extends ClassMemberAbstract
{
    /**
     * This method constructs a new statement based on the specified class constant.
     * @param PHPParser_Node_Stmt_ClassConst $node
     */
    public function __construct(PHPParser_Node_Stmt_ClassConst $node)
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
        // add the const line
        $this->addToLine($treeNode, 'const ');
        // add in the list of actual constants
        $treeNode = $this->processArgumentList($this->node->consts, $treeNode, new SimpleListLineBreak());
        // terminate the line
        $this->addToLine($treeNode, ';')->add(new HardLineBreak());
        return $treeNode;
    }
}
