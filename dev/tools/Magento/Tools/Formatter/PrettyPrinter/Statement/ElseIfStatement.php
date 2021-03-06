<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Elseif;

class ElseIfStatement extends AbstractConditionalStatement
{
    /**
     * This method constructs a new statement based on the specified elseif statement.
     * @param PHPParser_Node_Stmt_Elseif $node
     */
    public function __construct(PHPParser_Node_Stmt_Elseif $node)
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
        // use the base class to add in the conditional
        return $this->addConditional($treeNode, '} elseif');
    }
}
