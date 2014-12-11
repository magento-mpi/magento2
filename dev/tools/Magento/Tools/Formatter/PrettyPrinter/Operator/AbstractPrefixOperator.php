<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use Magento\Tools\Formatter\Tree\TreeNode;

abstract class AbstractPrefixOperator extends AbstractOperator
{
    /*
    protected function pPrefixOp($type, $operatorString, PHPParser_Node $node) {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $operatorString . $this->pPrec($node, $precedence, $associativity, 1);
    }
    */
    /**
     * Resolve the prefix operator for formatting
     *
     * @param TreeNode $treeNode
     * @return string
     */
    protected function resolvePrefixOperator(TreeNode $treeNode)
    {
        // Resolve the children according to precedence.
        $this->addToLine($treeNode, $this->operator());
        return $this->resolvePrecedence($this->expr(), $treeNode, 1);
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
        return $this->resolvePrefixOperator($treeNode);
    }

    /**
     * Return the expr as parsed by the parser lib.
     *
     * @return mixed
     */
    public function expr()
    {
        return $this->node->expr;
    }
}
