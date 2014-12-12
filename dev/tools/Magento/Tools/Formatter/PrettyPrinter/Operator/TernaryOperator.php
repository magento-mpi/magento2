<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Ternary;

class TernaryOperator extends AbstractLeftAssocOperator
{
    /**
     * Function: public function pExpr_Ternary(PHPParser_Node_Expr_Ternary $node) {
     *   // a bit of cheating: we treat the ternary as a binary op where the ?...: part is the operator.
     *   // this is okay because the part between ? and : never needs parentheses.
     *   return $this->pInfixOp('Expr_Ternary',
     *       $node->cond, ' ?' . (null !== $node->if ? ' ' . $this->p($node->if) . ' ' : '') . ': ', $node->else
     *   );
     * }
     *
     * @param PHPParser_Node_Expr_Ternary $node
     */
    public function __construct(PHPParser_Node_Expr_Ternary $node)
    {
        parent::__construct($node);
    }

    /**
     * This operator is a special case and requires us to override addOperatorToLine to build the operator up on the
     * fly then it can continue
     *
     * @param TreeNode $treeNode
     * @return void
     */
    protected function addOperatorToLine(TreeNode $treeNode)
    {
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        $line->add(' ?');
        if (null !== $this->node->if) {
            $line->add(' ');
            $this->resolveNode($this->node->if, $treeNode);
            $line->add(' ');
        }
        $line->add(': ');
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        // This should never be called because we have overridden resolve
        throw new \Exception('Ternary is not a normal operator, so this method does not apply');
    }

    /**
     * {@inheritdoc}
     */
    public function left()
    {
        return $this->node->cond;
    }

    /**
     * {@inheritdoc}
     */
    public function right()
    {
        return $this->node->else;
    }

    /* 'Expr_Ternary'          => array(14, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 14;
    }
}
