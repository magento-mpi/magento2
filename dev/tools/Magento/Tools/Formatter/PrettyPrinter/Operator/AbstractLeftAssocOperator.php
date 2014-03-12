<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
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
