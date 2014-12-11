<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

abstract class AbstractMathOperator extends AbstractLeftAssocOperator
{
    /**
     * Most math operators have the same precedence
     *
     * @return int
     */
    public function precedence()
    {
        return 4;
    }
}
