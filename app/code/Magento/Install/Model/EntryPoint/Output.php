<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Install_Model_EntryPoint_Output
{
    /**
     * Export variable
     *
     * @param mixed $var
     */
    public function export($var)
    {
        var_export($var);
    }

    /**
     * Display message
     *
     * @param string $message
     */
    public function success($message)
    {
        echo $message;
    }

    /**
     * Display error
     *
     * @param string $message
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function error($message)
    {
        echo $message;
        exit(1);
    }
}
