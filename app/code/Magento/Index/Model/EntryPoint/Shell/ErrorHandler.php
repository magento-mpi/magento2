<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_EntryPoint_Shell_ErrorHandler
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
