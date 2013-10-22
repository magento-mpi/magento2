<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgedeon
 * Date: 10/22/13
 * Time: 11:53 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;

abstract class InfixOperatorAbstract extends OperatorAbstract {
    /*
    protected function pInfixOp($type, PHPParser_Node $leftNode, $operatorString, PHPParser_Node $rightNode) {
        list($precedence, $associativity) = $this->precedenceMap[$type];

        return $this->pPrec($leftNode, $precedence, $associativity, -1)
        . $operatorString
        . $this->pPrec($rightNode, $precedence, $associativity, 1);
    }
    */
    protected function resolveInfixOperator(TreeNode $treeNode) {
        $left = $this->node->left;
        $right = $this->node->right;
        /** @var Line $line */
        $line = $treeNode->getData();
        // TODO: What to do if line is null here?
        // Resolve the children according to precedence.
        $this->resolvePrecedence($left, $treeNode, -1);
        $line->add($this->operator());
        $this->resolvePrecedence($right, $treeNode, 1);
    }
    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        $this->resolveInfixOperator($treeNode);
    }
}
