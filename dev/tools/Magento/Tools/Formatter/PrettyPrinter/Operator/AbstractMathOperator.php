<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
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
