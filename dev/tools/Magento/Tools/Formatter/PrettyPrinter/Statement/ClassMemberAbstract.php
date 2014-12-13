<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

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
