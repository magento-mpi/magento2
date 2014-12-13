<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

class InstanceofOperator extends AbstractInfixOperator
{
    /**
     * Original Code this is based on.
     *
     * Function: public function pExpr_Instanceof(PHPParser_Node_Expr_Instanceof $node) {
     *    return $this->pInfixOp('Expr_Instanceof', $node->expr, ' instanceof ', $node->class);
     * }
     *
     * @param \PHPParser_Node_Expr_Instanceof $node
     */
    public function __construct(\PHPParser_Node_Expr_Instanceof $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return 'instanceof';
    }

    /**
     * {@inheritdoc}
     */
    public function left()
    {
        return $this->node->expr;
    }

    /**
     * {@inheritdoc}
     */
    public function right()
    {
        return $this->node->class;
    }

    /* 'Expr_Instanceof'       => array( 2,  0), */
    /**
     * {@inheritdoc}
     */
    public function associativity()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 2;
    }
}
