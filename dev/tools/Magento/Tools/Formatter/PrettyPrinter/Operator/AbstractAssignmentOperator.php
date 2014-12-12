<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use Magento\Tools\Formatter\Tree\TreeNode;

abstract class AbstractAssignmentOperator extends AbstractInfixOperator
{
    /**
     * {@inheritdoc}
     */
    public function left()
    {
        return $this->node->var;
    }

    /**
     * {@inheritdoc}
     */
    public function right()
    {
        return $this->node->expr;
    }

    /**
     * We override this from the base class as Assignment operators should not have the conditional line break
     * like the other infix operators.
     *
     * @param TreeNode $treeNode
     * @return void
     */
    protected function addOperatorToLine(TreeNode $treeNode)
    {
        $this->addToLine($treeNode, ' ')->add($this->operator())->add(' ');
    }

    /**
     * Most Assignment operators have an associativity of 1
     *
     * @return int
     */
    public function associativity()
    {
        return 1;
    }

    /**
     * Most Assignment operators have an associativity of 15
     *
     * @return int
     */
    public function precedence()
    {
        return 15;
    }
}
