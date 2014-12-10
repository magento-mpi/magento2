<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use Magento\Tools\Formatter\Tree\TreeNode;

abstract class AbstractPostfixOperator extends AbstractOperator
{
    /*
    protected function pPrefixOp($type, $operatorString, PHPParser_Node $node) {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $operatorString . $this->pPrec($node, $precedence, $associativity, 1);
    }
    */
    /**
     * @param TreeNode $treeNode
     * @return TreeNode|string
     */
    protected function resolvePostfixOperator(TreeNode $treeNode)
    {
        // Resolve the children according to precedence.
        $treeNode = $this->resolvePrecedence($this->expr(), $treeNode, -1);
        $this->addToLine($treeNode, $this->operator());
        return $treeNode;
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     *
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        return $this->resolvePostfixOperator($treeNode);
    }

    /**
     * @return mixed
     */
    public function expr()
    {
        return $this->node->var;
    }
}
