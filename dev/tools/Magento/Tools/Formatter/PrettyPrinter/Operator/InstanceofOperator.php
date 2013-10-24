<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter\Operator;


class InstanceofOperator extends AbstractInfixOperator {
    /*
    public function pExpr_Instanceof(PHPParser_Node_Expr_Instanceof $node) {
        return $this->pInfixOp('Expr_Instanceof', $node->expr, ' instanceof ', $node->class);
    }
    */
    public function __construct(\PHPParser_Node_Expr_Instanceof $node)
    {
        parent::__construct($node);
    }

    public function operator()
    {
        return ' Instanceof ';
    }
    public function left()
    {
        return $this->node->expr;
    }
    public function right()
    {
        return $this->node->class;
    }
    /* 'Expr_Instanceof'       => array( 2,  0), */
    public function associativity()
    {
        return 0;
    }

    public function precedence()
    {
        return 2;
    }

}
