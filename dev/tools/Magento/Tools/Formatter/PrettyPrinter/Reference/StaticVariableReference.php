<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_StaticVar;

class StaticVariableReference extends AbstractVariableReference
{
    /**
     * This method constructs a new reference based on the specified property.
     * @param PHPParser_Node_Stmt_StaticVar $node
     */
    public function __construct(PHPParser_Node_Stmt_StaticVar $node)
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
        // add in the variable reference
        return $this->addVariableReference($treeNode);
    }
}
