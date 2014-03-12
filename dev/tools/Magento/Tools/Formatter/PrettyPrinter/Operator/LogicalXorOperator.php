<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_LogicalXor;

class LogicalXorOperator extends AbstractLeftAssocOperator
{
    /**
     * @param PHPParser_Node_Expr_LogicalXor $node
     */
    public function __construct(PHPParser_Node_Expr_LogicalXor $node)
    {
        parent::__construct($node);
    }

    /**
     * {@inheritdoc}
     */
    public function operator()
    {
        return 'xor';
    }

    /* 'Expr_LogicalXor'       => array(17, -1), */
    /**
     * {@inheritdoc}
     */
    public function precedence()
    {
        return 17;
    }
}
