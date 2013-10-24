<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_Ternary;

class TernaryOperator extends AbstractOperator
{
    /*
    public function pExpr_Ternary(PHPParser_Node_Expr_Ternary $node) {
        // a bit of cheating: we treat the ternary as a binary op where the ?...: part is the operator.
        // this is okay because the part between ? and : never needs parentheses.
        return $this->pInfixOp('Expr_Ternary',
            $node->cond, ' ?' . (null !== $node->if ? ' ' . $this->p($node->if) . ' ' : '') . ': ', $node->else
        );
    }
    */
    public function __construct(PHPParser_Node_Expr_Ternary $node)
    {
        parent::__construct($node);
    }

    /**
     * This operator is a special case and requires us to override resolve to build the operator up on the fly
     * then it can continue calling the super classes resolve.
     * @param TreeNode $treeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // Resolve the children according to precedence.
        $this->resolvePrecedence($this->left(), $treeNode, -1);
        $line->add(' ?');
        if (null !== $this->node->if) {
            $line->add(' ');
            $this->resolveNode($this->node->if, $treeNode);
            $line->add(' ');
        }
        $line->add(': ');
        $this->resolvePrecedence($this->right(), $treeNode, 1);
    }
    public function operator()
    {
        // This should never be called because we have overridden resolve
        throw new \Exception('Ternary is not a normal operator, so this method does not apply');
    }
    public function left()
    {
        return $this->node->cond;
    }
    public function right()
    {
        return $this->node->else;
    }
    /* 'Expr_Ternary'          => array(14, -1), */
    public function associativity()
    {
        return -1;
    }

    public function precedence()
    {
        return 14;
    }
}
