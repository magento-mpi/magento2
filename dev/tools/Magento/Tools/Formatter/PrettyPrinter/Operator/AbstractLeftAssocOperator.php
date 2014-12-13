<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

abstract class AbstractLeftAssocOperator extends AbstractInfixOperator
{
    /**
     * All Math Operators have the same associativity
     *
     * @return int
     */
    public function associativity()
    {
        return -1;
    }
}
