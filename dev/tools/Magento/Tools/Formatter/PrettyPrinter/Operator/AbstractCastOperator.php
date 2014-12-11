<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast;

abstract class AbstractCastOperator extends AbstractPrefixOperator
{
    /**
     * This member holds the operator for this cast.
     *
     * @var string $operator
     */
    protected $operator;

    /**
     * @param PHPParser_Node_Expr_Cast $node
     * @param string $operator
     */
    protected function __construct(PHPParser_Node_Expr_Cast $node, $operator)
    {
        parent::__construct($node);
        $this->operator = '(' . $operator . ')';
    }

    /**
     * All cast operators have the same associativity
     *
     * @return int
     */
    public function associativity()
    {
        return 1;
    }

    /**
     * This method returns the operator for this cast
     *
     * @return string String containing the entire cast operator.
     */
    public function operator()
    {
        return $this->operator;
    }

    /**
     * All cast operators have the same precedence
     *
     * @return int
     */
    public function precedence()
    {
        return 1;
    }
}
