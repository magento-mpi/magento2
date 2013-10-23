<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;


abstract class AbstractAssignmentOperator extends AbstractInfixOperator
{
    public function left()
    {
        return $this->node->var;
    }
    public function right()
    {
        return $this->node->expr;
    }
}
