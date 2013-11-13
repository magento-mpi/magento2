<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use Magento\Tools\Formatter\PrettyPrinter\InfixOperatorLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;

abstract class AbstractInfixOperator extends AbstractOperator
{
    /*
    protected function pInfixOp($type, PHPParser_Node $leftNode, $operatorString, PHPParser_Node $rightNode) {
        list($precedence, $associativity) = $this->precedenceMap[$type];

        return $this->pPrec($leftNode, $precedence, $associativity, -1)
        . $operatorString
        . $this->pPrec($rightNode, $precedence, $associativity, 1);
    }
    */
    protected function resolveInfixOperator(TreeNode $treeNode)
    {
        // Resolve the children according to precedence.
        $this->resolvePrecedence($this->left(), $treeNode, -1);
        $this->addOperatorToLine($treeNode);
        return $this->resolvePrecedence($this->right(), $treeNode, 1);
    }

    /**
     * This method adds the operator to the line object with any required line breaks
     */
    protected function addOperatorToLine(TreeNode $treeNode)
    {
        $this->addToLine($treeNode, ' ')->add($this->operator())->add(new InfixOperatorLineBreak($this));
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        return $this->resolveInfixOperator($treeNode);
    }

    public function left()
    {
        return $this->node->left;
    }

    public function right()
    {
        return $this->node->right;
    }
}
