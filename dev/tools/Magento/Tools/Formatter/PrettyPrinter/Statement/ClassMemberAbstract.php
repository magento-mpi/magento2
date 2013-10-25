<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node_Stmt;

abstract class ClassMemberAbstract extends AbstractStatement
{
    /**
     * We should trim these comments
     * @return bool
     */
    public function isTrimComments()
    {
        return true;
    }
}
