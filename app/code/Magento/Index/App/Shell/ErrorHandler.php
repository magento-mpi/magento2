<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\App\Shell;

class ErrorHandler
{
    /**
     * Terminate execution of the script
     *
     * @param int|string $status
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function terminate($status)
    {
        exit($status);
    }
}
