<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;


abstract class AssignmentOperatorAbstract extends InfixOperatorAbstract
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
