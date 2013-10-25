<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;


abstract class AbstractCastOperator extends AbstractPrefixOperator
{
    /**
     * All cast operators have the same associativity
     * @return int
     */
    public function associativity()
    {
        return 1;
    }
    /**
     * All cast operators have the same precedence
     * @return int
     */
    public function precedence()
    {
        return 1;
    }
}
