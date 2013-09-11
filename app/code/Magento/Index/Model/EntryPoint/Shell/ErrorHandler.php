<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\EntryPoint\Shell;

class ErrorHandler
{
    /**
     * Terminate execution of the script
     *
     * @param int|string $status
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function terminate($status)
    {
        exit($status);
    }
}
